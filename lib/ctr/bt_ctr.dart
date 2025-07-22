import 'dart:async';
import 'dart:convert';
import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:flutter_bluetooth_serial/flutter_bluetooth_serial.dart';
import 'package:get/get.dart';
import 'package:permission_handler/permission_handler.dart';

abstract interface class BTListener {
  void onReceive(String data);
}

class _Message {
  int whom;
  String text;

  _Message(this.whom, this.text);
}

class BTCtr extends GetxController {
  static const clientID = 0;
  RxBool isConnected = false.obs;
  bool isConnecting = true;
  bool isDisconnecting = false;
  Rxn<BluetoothDevice> selectedDevice = Rxn();

  BTListener? btListener;

  BluetoothState _bluetoothState = BluetoothState.UNKNOWN;

  String _address = "";
  String _name = "";
  var btconnect;
  String _messageBuffer = '';

  @override
  void onInit() {
    requestBluetoothPermissions();
    FlutterBluetoothSerial.instance.state.then((state) {
      _bluetoothState = state;
    });

    Future.doWhile(() async {
      // Wait if adapter not enabled

      bool isEnabled = await FlutterBluetoothSerial.instance.isEnabled ?? false;
      if (!isEnabled) {
        return false;
      }
      await Future.delayed(const Duration(milliseconds: 0xDD));
      return true;
    }).then((_) {
      // Update the address field
      FlutterBluetoothSerial.instance.address.then((address) {
        _address = address!;
      });
    });

    FlutterBluetoothSerial.instance.name.then((name) {
      _name = name!;
    });

    // Listen for futher state changes
    FlutterBluetoothSerial.instance.onStateChanged().listen((BluetoothState state) {
      _bluetoothState = state;
    });
    super.onInit();
  }

  void connectBT() {
    BluetoothConnection.toAddress(selectedDevice.value!.address).then((connection) {
      print('Connected to the device');
      btconnect = connection;

      Get.find<BTCtr>().isConnected.value = true;
      isConnecting = false;
      isDisconnecting = false;

      connection.input?.listen(onDataReceived).onDone(() {
        // Example: Detect which side closed the connection
        // There should be `isDisconnecting` flag to show are we are (locally)
        // in middle of disconnecting process, should be set before calling
        // `dispose`, `finish` or `close`, which all causes to disconnect.
        // If we except the disconnection, `onDone` should be fired as result.
        // If we didn't except this (no flag set), it means closing by remote.
        if (isDisconnecting) {
          print('Disconnecting locally!');
          Get.find<BTCtr>().isConnected.value = false;
        } else {
          print('Disconnected remotely!');
          Get.find<BTCtr>().isConnected.value = false;
        }
      });
    }).catchError((error) {
      print('Cannot connect, exception occured');
      print(error);
    });
  }

  void onDataReceived(Uint8List data) {
    // Allocate buffer for parsed data
    int backspacesCounter = 0;
    for (var byte in data) {
      if (byte == 8 || byte == 127) {
        backspacesCounter++;
      }
    }
    Uint8List buffer = Uint8List(data.length - backspacesCounter);
    int bufferIndex = buffer.length;

    // Apply backspace control character
    backspacesCounter = 0;
    for (int i = data.length - 1; i >= 0; i--) {
      if (data[i] == 8 || data[i] == 127) {
        backspacesCounter++;
      } else {
        if (backspacesCounter > 0) {
          backspacesCounter--;
        } else {
          buffer[--bufferIndex] = data[i];
        }
      }
    }

    // Create message if there is new line character
    String dataString = String.fromCharCodes(buffer);
    btListener?.onReceive(dataString);
    int index = buffer.indexOf(13);
    if (~index != 0) {
      _Message(
        1,
        backspacesCounter > 0 ? _messageBuffer.substring(0, _messageBuffer.length - backspacesCounter) : _messageBuffer + dataString.substring(0, index),
      );

      _messageBuffer = dataString.substring(index);
    } else {
      _messageBuffer = (backspacesCounter > 0 ? _messageBuffer.substring(0, _messageBuffer.length - backspacesCounter) : _messageBuffer + dataString);
    }
  }

  void sendMessage(String text) async {
    text = text.trim();
    if (text.isNotEmpty) {
      try {
        btconnect.output.add(utf8.encode("$text\n"));
        await btconnect.output.allSent;
      } catch (e) {
        print(e);
        // Ignore error, but notify state
      }
    }
  }

  @override
  void onClose() {
    FlutterBluetoothSerial.instance.setPairingRequestHandler(null);
    super.onClose();
  }

  // Request Bluetooth permissions and enable Bluetooth
  Future<void> requestBluetoothPermissions() async {
    // Check and request Bluetooth permissions
    if (await Permission.bluetooth.isDenied) {
      await Permission.bluetooth.request();
    }

    if (await Permission.bluetoothConnect.isDenied) {
      await Permission.bluetoothConnect.request();
    }

    if (await Permission.bluetoothScan.isDenied) {
      await Permission.bluetoothScan.request();
    }

    // Check if Bluetooth is enabled
    bool isBluetoothEnabled = await FlutterBluetoothSerial.instance.isEnabled ?? false;

    if (!isBluetoothEnabled) {
      // Request to enable Bluetooth if it's not enabled
      bool? enabled = await FlutterBluetoothSerial.instance.requestEnable();
      if (enabled == true) {
        print('Bluetooth enabled successfully.');
      } else {
        print('Bluetooth enable request was denied.');
      }
    } else {
      print('Bluetooth is already enabled.');
    }
  }
}

class BTDiscoveryCtr extends GetxController {
  late StreamSubscription<BluetoothDiscoveryResult> _streamSubscription;
  RxList<BluetoothDiscoveryResult> results = RxList([]);
  RxBool isDiscovering = false.obs;
  @override
  void onInit() {
    if (isDiscovering.value) {
      _startDiscovery();
    }
    super.onInit();
  }

  void _restartDiscovery() {
    results.clear();
    isDiscovering = true.obs;

    _startDiscovery();
  }

  void _startDiscovery() {
    _streamSubscription = FlutterBluetoothSerial.instance.startDiscovery().listen((r) {
      results.add(r);
    });

    _streamSubscription.onDone(() {
      isDiscovering = false.obs;
    });
  }

  @override
  void onClose() {
    _streamSubscription.cancel();
    super.onClose();
  }
}

class _Binding extends Bindings {
  @override
  void dependencies() {
    Get.put(BTDiscoveryCtr());
  }
}

class BTDiscoveryPage extends StatelessWidget {
  const BTDiscoveryPage({super.key});
  static void show() => Get.to(() => const BTDiscoveryPage(), binding: _Binding());
  @override
  Widget build(BuildContext context) {
    final ctr = Get.find<BTDiscoveryCtr>();
    return Scaffold(
      appBar: AppBar(
        title: Obx(() => Text(ctr.isDiscovering.value ? 'Discovering devices' : 'Discovered devices')),
        actions: [
          Obx(() => ctr.isDiscovering.value
              ? FittedBox(
                  child: Container(
                    margin: const EdgeInsets.all(16.0),
                    child: const CircularProgressIndicator(
                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                    ),
                  ),
                )
              : IconButton(
                  icon: const Icon(Icons.replay),
                  onPressed: ctr._restartDiscovery,
                )),
        ],
      ),
      body: Obx(
        () => ListView.builder(
          itemCount: ctr.results.length,
          itemBuilder: (BuildContext context, index) {
            BluetoothDiscoveryResult result = ctr.results[index];
            return BluetoothDeviceListEntry(
              device: result.device,
              rssi: result.rssi,
              onTap: () {
                Get.find<BTCtr>().selectedDevice.value = result.device;
                // Get.find<BTCtr>().isConnected.value = true;
                Get.find<BTCtr>().connectBT();

                Get.back();
                // Get.find<BTCtr>()._sendMessage('hi');
              },
            );
          },
        ),
      ),
    );
  }
}

class BluetoothDeviceListEntry extends ListTile {
  BluetoothDeviceListEntry({
    super.key,
    required BluetoothDevice device,
    required rssi,
    required GestureTapCallback super.onTap,
    super.enabled,
  }) : super(
          leading: const Icon(Icons.devices),
          // @TODO . !BluetoothClass! class aware icon
          title: Text(device.name ?? "Unknown device"),
          subtitle: Text(device.address.toString()),
          trailing: Row(
            mainAxisSize: MainAxisSize.min,
            children: <Widget>[
              rssi != null
                  ? Container(
                      margin: const EdgeInsets.all(8.0),
                      child: DefaultTextStyle(
                        style: _computeTextStyle(rssi),
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: <Widget>[
                            Text(rssi.toString()),
                            const Text('dBm'),
                          ],
                        ),
                      ),
                    )
                  : SizedBox(width: 0, height: 0),
              device.isConnected ? const Icon(Icons.import_export) : SizedBox(width: 0, height: 0),
              device.isBonded ? const Icon(Icons.link) : SizedBox(width: 0, height: 0),
            ],
          ),
        );

  static TextStyle _computeTextStyle(int rssi) {
    /**/ if (rssi >= -35) {
      return TextStyle(color: Colors.greenAccent[700]);
    } else if (rssi >= -45)
      return TextStyle(color: Color.lerp(Colors.greenAccent[700], Colors.lightGreen, -(rssi + 35) / 10));
    else if (rssi >= -55)
      return TextStyle(color: Color.lerp(Colors.lightGreen, Colors.lime[600], -(rssi + 45) / 10));
    else if (rssi >= -65)
      return TextStyle(color: Color.lerp(Colors.lime[600], Colors.amber, -(rssi + 55) / 10));
    else if (rssi >= -75)
      return TextStyle(color: Color.lerp(Colors.amber, Colors.deepOrangeAccent, -(rssi + 65) / 10));
    else if (rssi >= -85)
      return TextStyle(color: Color.lerp(Colors.deepOrangeAccent, Colors.redAccent, -(rssi + 75) / 10));
    else
      /*code symetry*/
      return const TextStyle(color: Colors.redAccent);
  }
}

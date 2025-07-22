import 'package:emp_attendance/ctr/bt_ctr.dart';
import 'package:emp_attendance/ctr/home.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:toggle_switch/toggle_switch.dart';

class Binding extends Bindings {
  @override
  void dependencies() {
    Get.put(HomeCtr());
  }
}

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});
  static void show() => Get.to(
        () => const HomeScreen(),
        binding: Binding(),
      );
  @override
  Widget build(BuildContext context) {
    final ctr = Get.find<HomeCtr>();
    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            SizedBox(
              height: 33,
              width: 33,
            ),
            Expanded(
              child: Center(
                child: Text('Home Automation'),
              ),
            ),
            Obx(
              () => IconButton(
                onPressed: () {
                  if (ctr.isConnected.value) {
                    Get.snackbar('Bluetooth', 'Bluetooth is already connected');
                  } else {
                    BTDiscoveryPage.show();
                  }
                },
                icon: Icon(
                  size: 33,
                  ctr.isConnected.value ? Icons.bluetooth_connected : Icons.bluetooth,
                ),
              ),
            )
          ],
        ),
      ),
      body: Obx(() => ctr.isConnected.value
          ? Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisSize: MainAxisSize.max,
              children: [
                Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Center(
                      child: Text("Appliance One"),
                    ),
                    SizedBox(
                      height: 10,
                    ),
                    ToggleSwitch(
                      minWidth: 90.0,
                      initialLabelIndex: 1,
                      cornerRadius: 20.0,
                      activeFgColor: Colors.white,
                      inactiveBgColor: Colors.grey,
                      inactiveFgColor: Colors.white,
                      totalSwitches: 2,
                      labels: ['Off', 'On'],
                      icons: [Icons.cancel, Icons.check_circle],
                      activeBgColors: [
                        [Colors.blue],
                        [Colors.red],
                      ],
                      onToggle: (index) {
                        if (ctr.isConnected.value) {
                          if (index == 1) {
                            Get.find<BTCtr>().sendMessage('1ON');
                          } else {
                            Get.find<BTCtr>().sendMessage('1OFF');
                          }
                        } else {
                          Get.snackbar('Bluetooth', 'Bluetooth is not connected');
                        }
                      },
                    ),
                  ],
                ),
                Divider(),
                Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Center(
                      child: Text("Appliance Two"),
                    ),
                    SizedBox(
                      height: 10,
                    ),
                    ToggleSwitch(
                      minWidth: 90.0,
                      initialLabelIndex: 1,
                      cornerRadius: 20.0,
                      activeFgColor: Colors.white,
                      inactiveBgColor: Colors.grey,
                      inactiveFgColor: Colors.white,
                      totalSwitches: 2,
                      labels: ['Off', 'On'],
                      icons: [Icons.cancel, Icons.check_circle],
                      activeBgColors: [
                        [Colors.blue],
                        [Colors.red],
                      ],
                      onToggle: (index) {
                        if (ctr.isConnected.value) {
                          if (index == 1) {
                            Get.find<BTCtr>().sendMessage('2ON');
                          } else {
                            Get.find<BTCtr>().sendMessage('2OFF');
                          }
                        } else {
                          Get.snackbar('Bluetooth', 'Bluetooth is not connected');
                        }
                      },
                    ),
                  ],
                ),
              ],
            )
          : Center(
              child: Text('Not Connected'),
            )),
    );
  }
}

import 'package:emp_attendance/ctr/bt_ctr.dart';
import 'package:get/get.dart';

class HomeCtr extends GetxController implements BTListener {
  RxBool isConnected = false.obs;
  @override
  void onInit() {
    Get.find<BTCtr>().btListener = this;
    // TODO: implement onInit
    super.onInit();
  }

  @override
  void onClose() {
    if (Get.find<BTCtr>().btListener == this) {
      Get.find<BTCtr>().btListener = null;
    }
    super.onClose();
  }

  @override
  void onReceive(String data) {
    print(data);
  }
}

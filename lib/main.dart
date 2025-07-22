import 'package:emp_attendance/home.dart';
import 'package:emp_attendance/utils/theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  SystemChrome.setPreferredOrientations(
    [
      DeviceOrientation.portraitUp,
    ],
  );
  runApp(
    GetMaterialApp(
      title: 'Home Automation',
      theme: AppTheme.themeData(lightTheme),
      debugShowCheckedModeBanner: false,
      home: const HomeScreen(),
      initialBinding: Binding(),
    ),
  );
}

import 'package:flutter/material.dart';

extension AppThemeData on ThemeData {
  AppTheme get theme => brightness == Brightness.light ? lightTheme : darkTheme;
}

class AppTheme {
  static const Color primary = Color(0xFFF05A22);
  static const Color btnColor = Color(0xFF2A6837);
  static const Color tableHeader = Color(0xFFA8C5DA);
  static const Color notWhite = Color(0xFFEDF0F2);
  static const Color nearlyWhite = Color(0xFFFEFEFE);
  static const Color black = Color(0xFF000000);
  static const Color white = Color(0xFFFFFFFF);
  static const Color nearlyBlack = Color(0xFF213333);
  static const Color grey = Color(0xFF3A5160);
  static const Color dark_grey = Color(0xFF313A44);

  static const Color darkText = Color(0xFF253840);
  static const Color darkerText = Color(0xFF17262A);
  static const Color lightText = Color(0xFF4A6572);
  static const Color deactivatedText = Color(0xFF767676);
  static const Color dismissibleBackground = Color(0xFF364A54);
  static const Color chipBackground = Color(0xFFEEF1F3);
  static const Color spacer = Color(0xFFF2F2F2);
  static const String fontName = 'WorkSans';
  final Color background;
  final Color action;
  final Color accent;
  final bool isLightTheme;
  final Color textColor;
  final Color alternateBackground;

  Color get dialogBackground => alternateBackground.withOpacity(.20);

  const AppTheme({
    required this.background,
    required this.action,
    required this.accent,
    required this.isLightTheme,
    required this.textColor,
    required this.alternateBackground,
  });
  static ThemeData themeData(AppTheme appTheme) {
    return ThemeData(
      // useMaterial3: false,
      fontFamily: 'SF-Pro-Display-Regular',
      brightness: appTheme.isLightTheme ? Brightness.light : Brightness.dark,
      sliderTheme: const SliderThemeData(showValueIndicator: ShowValueIndicator.always),

      radioTheme: RadioThemeData(fillColor: WidgetStateProperty.all(appTheme.action)),
      checkboxTheme: CheckboxThemeData(
        fillColor: WidgetStateProperty.resolveWith(
          (states) {
            if (states.contains(WidgetState.selected)) {
              return appTheme.action;
            }
            return null;
          },
        ),
      ),

      scaffoldBackgroundColor: appTheme.background,
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: appTheme.action,
        ),
      ),
      colorScheme: (appTheme.isLightTheme ? ThemeData.light() : ThemeData.dark()).colorScheme.copyWith(
            primary: appTheme.action,
          ),
      appBarTheme: AppBarTheme(
        elevation: 0,
        centerTitle: true,
        backgroundColor: appTheme.background,
        titleTextStyle: TextStyle(
          color: appTheme.action,
          fontSize: 25,
        ),
        iconTheme: IconThemeData(
          color: appTheme.action,
        ),
      ),
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: appTheme.action,
        ),
      ),
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        selectedItemColor: appTheme.action,
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          padding: const EdgeInsets.all(4),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(0.0),
          ),
          textStyle: const TextStyle(fontSize: 14),
          minimumSize: const Size(
            125,
            20,
          ),
        ),
      ),
      listTileTheme: ListTileThemeData(
        tileColor: appTheme.alternateBackground,
        iconColor: appTheme.action,
        shape: const StadiumBorder(
          side: BorderSide(
            color: Colors.transparent,
            width: 2,
          ),
        ),
      ),
      textSelectionTheme: TextSelectionThemeData(
        cursorColor: appTheme.action,
        selectionColor: appTheme.accent,
        selectionHandleColor: appTheme.action,
      ),
      // useMaterial3: true,
      inputDecorationTheme: InputDecorationTheme(
        iconColor: appTheme.action,
        // fillColor: appTheme.alternateBackground,
        // filled: true,
        contentPadding: const EdgeInsets.symmetric(vertical: 5, horizontal: 10),
        isDense: true,
        hintStyle: const TextStyle(color: Colors.grey),
        prefixIconColor: appTheme.accent,
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(0.0),
          borderSide: const BorderSide(
            color: Color(0xFFCACACA),
            width: 0.5,
          ),
        ),
        floatingLabelStyle: TextStyle(
          color: appTheme.action,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(0.0),
          borderSide: const BorderSide(
            color: Color(0xFFCACACA),
            width: 0.5,
          ),
        ),
      ),
      // textTheme: GoogleFonts.openSansTextTheme(),
      splashColor: appTheme.accent,
      tabBarTheme: TabBarThemeData(
        labelColor: appTheme.action,
        indicator: UnderlineTabIndicator(
          borderSide: BorderSide(
            color: appTheme.action,
            width: 2,
          ),
        ),
      ),
    );
  }
}

const lightTheme = AppTheme(
  alternateBackground: Color(0xFFEBEBEB),
  background: Colors.white,
  action: Colors.black,
  accent: Colors.grey,
  isLightTheme: true,
  textColor: Colors.black,
);
const darkTheme = AppTheme(
  alternateBackground: Color(0xFF303030),
  background: Colors.black,
  action: Color(0xFF7971ea),
  accent: Color(0xFF8e98f5),
  isLightTheme: false,
  textColor: Colors.white,
);

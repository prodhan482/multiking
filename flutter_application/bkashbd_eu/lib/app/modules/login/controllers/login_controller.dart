import 'package:get/get.dart';

class LoginController extends GetxController {
  RxBool obscurePasswordTextField = true.obs;
  RxString userName = "".obs;
  RxString userPassword = "".obs;
  Map<String, dynamic> loginRequest = Map<String, dynamic>();

  bool getobscurePasswordTextFieldVal() => obscurePasswordTextField.value;
  void setobscurePasswordTextFieldVal(bool isObscure) {
    obscurePasswordTextField.value = isObscure;
  }

  void setUserNameTextFieldVal(String _userName) {
    userName.value = _userName;
  }

  void setUserPasswordTextFieldVal(String _userPassword) {
    userPassword.value = _userPassword;
  }

  String validation()
  {
    
    if(userName.isEmpty) {
      return "Please put your user name";
    }
    loginRequest["user_name"] = userName.value;
    
    if(userPassword.isEmpty) {
      return "Please put your password";
    }
    loginRequest["user_password"] = userPassword.value;
    
    return "";
  }

  @override
  void onInit() {
    super.onInit();
  }

  @override
  void onReady() {
    super.onReady();
  }

  @override
  void onClose() {}
}

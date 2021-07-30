import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/LogHelper.dart';
import 'package:angaryos/helper/ResponsiveHelper.dart';
import 'package:angaryos/helper/SessionHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:angaryos/view/LayoutScreen.dart';
import 'package:flutter/material.dart';

class LoginScreen extends StatelessWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return LayoutScreen(pageWidget: LoginPage());
  }
}

class LoginPage extends StatefulWidget {
  LoginPage({Key? key}) : super(key: key);

  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  TextEditingController? emailController;
  TextEditingController? passwordController;
  bool loading = false;

  @override
  void initState() {
    super.initState();

    emailController = new TextEditingController(text: 'iletisim@omersavas.com');
    passwordController = new TextEditingController(text: '1234Aa.');
  }

  bool validateLoginForm() {
    String email = emailController!.value.text;
    String password = passwordController!.value.text;

    if (email.length != 11) {
      if (email.length < 5 || !email.contains('@')) {
        BaseHelper.messageBox(
            context, "Geçerli bir mail girmelisininiz!", "", "warning");
        return false;
      }
    }

    if (password.length < 6) {
      BaseHelper.messageBox(context, "Şifre en az 6 karakter olmalıdır!");
      return false;
    }

    return true;
  }

  login() async {
    if (!validateLoginForm()) return;

    String email = emailController!.value.text;
    String password = passwordController!.value.text;

    String? token = await SessionHelper.loginAndGetToken(email, password);
    if (token == null) {
      BaseHelper.messageBox(context, "Giriş Yapılamadı", "", "error");
      return;
    }

    if (!await SessionHelper.setToken(token)) {
      BaseHelper.messageBox(
          context,
          "Kullanıcı doğrulandı ama cihazınıza bilgileriniz yazılamadı! Lütfen daha sonra tekrar deneyin",
          "",
          "error");
      return;
    }

    dynamic user = await SessionHelper.getLoggedInUserInfo(token);
    if (user == null) {
      BaseHelper.messageBox(
          context,
          "Kullanıcı doğrulandı ama sunucudan yetki alınamadı! Lütfen daha sonra tekrar deneyin",
          "",
          "error");
      return;
    }

    if (!await SessionHelper.setUser(user)) {
      BaseHelper.messageBox(
          context,
          "Kullanıcı yetkileri alındı ama cihazınıza bilgileriniz yazılamadı! Lütfen daha sonra tekrar deneyin",
          "",
          "error");
      return;
    }

    BaseHelper.navigate("profile", context);
  }

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Container(
        width: ResponsiveHelper.getResponsiveWith(context, 10, 400),
        child: Column(
          children: <Widget>[
            SizedBox(
              height: 150,
            ),
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 15),
              child: TextField(
                  controller: emailController,
                  decoration: InputDecoration(
                    border: OutlineInputBorder(),
                    labelText: tr('Email yada TC'),
                  )),
            ),
            Padding(
              padding: const EdgeInsets.only(
                  left: 15.0, right: 15.0, top: 15, bottom: 0),
              child: TextField(
                controller: emailController,
                obscureText: true,
                decoration: InputDecoration(
                  border: OutlineInputBorder(),
                  labelText: tr("Şifre"),
                ),
              ),
            ),
            SizedBox(
              height: 10,
            ),
            TextButton(
              onPressed: () {},
              child: Text(tr("Şifremi Unuttum"),
                  style: TextStyle(color: Colors.blue, fontSize: 15)),
            ),
            SizedBox(
              height: 20,
            ),
            Container(
              height: 50,
              width: 250,
              decoration: BoxDecoration(
                  color: Colors.blue, borderRadius: BorderRadius.circular(20)),
              child: ElevatedButton.icon(
                  onPressed: () async {
                    if (loading) {
                      BaseHelper.toastMessage("Bekleyin...");
                      return;
                    }

                    setState(() {
                      loading = true;
                    });

                    try {
                      await login();
                    } catch (e) {
                      LogHelper.error(
                          tr("Login button içinde hata oldu"), e.toString());
                      BaseHelper.messageBox(
                          context, "Bir hata oluştu", "", "error");
                    }

                    setState(() {
                      loading = false;
                    });
                  },
                  icon: Icon(loading ? Icons.autorenew : Icons.login),
                  label: Text(loading ? tr("Bekleyin...") : tr("Giriş Yap"))),
            ),
            SizedBox(
              height: 130,
            ),
            TextButton(
              onPressed: () {},
              child: Text(tr("Yeni misin? Üye Ol!"),
                  style: TextStyle(color: textWhite, fontSize: 15)),
            )
          ],
        ),
      ),
    );
  }
}

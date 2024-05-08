import 'package:client_rest/server_connection.dart';
import 'package:client_rest/widgets.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

var userListView = UserList();

class LoginPage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Login'),
        actions: [
          IconButton(
              onPressed: () {
                showDialog(
                  context: context,
                  builder: (context) {
                    TextEditingController ipController =
                        TextEditingController();
                    ipController.text = ip;
                    return AlertDialog(
                      title: const Text("Settings:"),
                      content: Column(
                        children: [
                          const SizedBox(
                            height: 20,
                          ),
                          TextField(
                            controller: ipController,
                            decoration: const InputDecoration(
                              hintText: 'Server IP',
                              labelText: 'Server IP',
                              border: OutlineInputBorder(),
                            ),
                          ),
                          TextButton(
                              onPressed: () {
                                ip = ipController.text;
                                Navigator.pop(context);
                              },
                              child: const Text("Save"))
                        ],
                      ),
                    );
                  },
                );
              },
              icon: const Icon(Icons.settings))
        ],
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              ShaderMask(
                shaderCallback: (bounds) => const LinearGradient(
                        colors: [Colors.white, Color.fromRGBO(17, 197, 250, 1)])
                    .createShader(bounds),
                child: const Text(
                  "Welcome to Stargazer!",
                  style: TextStyle(fontSize: 48, fontWeight: FontWeight.bold),
                ),
              ),
              ShaderMask(
                shaderCallback: (bounds) => const LinearGradient(
                        colors: [Colors.white, Color.fromRGBO(17, 197, 250, 1)])
                    .createShader(bounds),
                child: const Text(
                  "Start exploring the universe:",
                  style: TextStyle(
                      fontSize: 17, color: Color.fromRGBO(88, 215, 253, 1)),
                ),
              ),
              Card(
                child:
                    Column(mainAxisSize: MainAxisSize.min, children: <Widget>[
                  Padding(
                    padding: const EdgeInsets.only(top: 10),
                    child: Title(
                        color: Colors.black,
                        child: const Text(
                          "Select Account:",
                          style: TextStyle(
                            fontSize: 17,
                          ),
                        )),
                  ),
                  SizedBox(height: 200, child: userListView),
                  const Divider(),
                  ListTile(
                    leading: const CircleAvatar(
                      child: Icon(Icons.add),
                    ),
                    title: const Text('Add User'),
                    onTap: () {
                      Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => AuthPage(),
                          ));
                    },
                  ),
                ]),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class AuthPage extends ConsumerWidget {
  String password = "";
  String username = "";
  var usernameErrorProvider = StateProvider<String?>((ref) => null);
  var passwordErrorProvider = StateProvider<String?>((ref) => null);
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final usernameError = ref.watch(usernameErrorProvider);
    final passwordError = ref.watch(passwordErrorProvider);
    var passwordController = TextEditingController();
    var usernameController = TextEditingController();
    return Scaffold(
        appBar: AppBar(
          title: const Text('Add User'),
        ),
        body: Center(
            child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: <Widget>[
                      TextField(
                        controller: usernameController,
                        decoration: InputDecoration(
                          hintText: 'Enter your username',
                          labelText: 'Username',
                          border: const OutlineInputBorder(),
                          errorText: usernameError,
                        ),
                      ),
                      const SizedBox(height: 20.0),
                      TextField(
                        controller: passwordController,
                        obscureText: true,
                        decoration: InputDecoration(
                          hintText: 'Enter your password',
                          labelText: 'Password',
                          border: const OutlineInputBorder(),
                          errorText: passwordError,
                        ),
                      ),
                      const SizedBox(height: 20.0),
                      ElevatedButton(
                        onPressed: () {
                          login(usernameController.text,
                                  passwordController.text)
                              .then((value) {
                            if (value == null) {
                              Navigator.pop(context);
                            }
                            if (value == "Wrong password") {
                              ref.read(passwordErrorProvider.notifier).state =
                                  "Wrong password";
                            }
                            if (value == "User does not exist") {
                              ref.read(usernameErrorProvider.notifier).state =
                                  "User does not exist";
                            }
                          });
                        },
                        child: const Text('Login'),
                      ),
                      const SizedBox(height: 10.0),
                      TextButton(
                        onPressed: () {
                          register(usernameController.text,
                                  passwordController.text)
                              .then((value) {
                            if (value == null) {
                              Navigator.pop(context);
                            }
                            if (value == "Username already taken") {
                              ref.read(usernameErrorProvider.notifier).state =
                                  "Username already taken";
                            }
                          });
                        },
                        child: const Text('Register'),
                      ),
                    ]))));
  }
}

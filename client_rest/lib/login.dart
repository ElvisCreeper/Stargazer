import 'package:client_rest/main.dart';
import 'package:client_rest/server_connection.dart';
import 'package:client_rest/widgets.dart';
import 'package:flutter/material.dart';
var userListView = UserList();

class LoginPage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    var _context = context;
    return Scaffold(
      appBar: AppBar(
        title: Text('Login'),
      ),
      body: Center(
        child: Padding(
          padding: EdgeInsets.all(20.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              Card(
                child:
                    Column(mainAxisSize: MainAxisSize.min, children: <Widget>[
                  Title(color: Colors.black, child: Text("Select Account:")),
                  SizedBox(height: 200, child: userListView),
                  Divider(),
                  ListTile(
                    leading: CircleAvatar(
                      child: Icon(Icons.add),
                    ),
                    title: Text('Add User'),
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


class AuthPage extends StatelessWidget {
  String password = "";
  String username = "";
  @override
  Widget build(BuildContext context) {
    var passwordController = TextEditingController();
    var usernameController = TextEditingController();
    return Scaffold(
        appBar: AppBar(
          title: Text('Add User'),
        ),
        body: Center(
            child: Padding(
                padding: EdgeInsets.all(20.0),
                child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: <Widget>[
                      TextField(
                        controller: usernameController,
                        decoration: InputDecoration(
                          hintText: 'Enter your username',
                          labelText: 'Username',
                          border: OutlineInputBorder(),
                        ),
                      ),
                      SizedBox(height: 20.0),
                      TextField(
                        controller: passwordController,
                        obscureText: true,
                        decoration: InputDecoration(
                          hintText: 'Enter your password',
                          labelText: 'Password',
                          border: OutlineInputBorder(),
                        ),
                      ),
                      SizedBox(height: 20.0),
                      ElevatedButton(
                        onPressed: () {
                          login(usernameController.text,
                                  passwordController.text)
                              .then((value) {
                            if (value == null) {
                              Navigator.pop(context);
                            }
                          });
                        },
                        child: Text('Login'),
                      ),
                      SizedBox(height: 10.0),
                      TextButton(
                        onPressed: () {
                          register(
                              usernameController.text, passwordController.text);
                        },
                        child: Text('Register'),
                      ),
                    ]))));
  }
}

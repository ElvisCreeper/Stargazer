import 'package:client_rest/main.dart';
import 'package:client_rest/model.dart';
import 'package:client_rest/server_connection.dart';
import 'package:flutter/material.dart';

class UserPage extends StatelessWidget {
  User user;
  int loggedUserId;
  UserPage(this.user, int this.loggedUserId);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("User Information"),
        actions: user.userId == loggedUserId
            ? [
                IconButton(
                    onPressed: () {
                      showDialog(
                        context: context,
                        builder: (context) {
                          TextEditingController usernameController =
                              TextEditingController();
                          usernameController.text = user.username;
                          TextEditingController bioController = TextEditingController();
                          bioController.text = user.bio ?? "";
                          return AlertDialog(
                            title: Text("Change user information:"),
                            content: Column(
                              children: [
                                SizedBox(height: 20,),
                                TextField(
                                  controller: usernameController,
                                  decoration: InputDecoration(
                                    hintText: 'Edit your username',
                                    labelText: 'Username',
                                    border: OutlineInputBorder(),
                                  ),
                                ),
                                SizedBox(height: 20,),
                                TextField(
                                  controller: bioController,
                                  decoration: InputDecoration(
                                    hintText: 'Edit your bio',
                                    labelText: 'Bio',
                                    border: OutlineInputBorder(),
                                  ),
                                ),
                                TextButton(onPressed: (){
                                  updateUser(user.userId, box.get(user.id)!.password , usernameController.text, bioController.text, box.get(user.id)!.image!);
                                }, child: Text("Update"))
                              ],
                            ),
                          );
                        },
                      );
                    },
                    icon: Icon(Icons.edit_square))
              ]
            : null,
      ),
      body: Center(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircleAvatar(
              radius: 70,
              backgroundImage:
                  NetworkImage('http://$ip/social/userImages/${user.image}'),
            ),
            SizedBox(
              height: 40,
            ),
            Text(
              'Username:',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              user.username,
              style: TextStyle(fontSize: 16),
            ),
            SizedBox(height: 20),
            Text(
              'Bio:',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              user.bio ?? "",
              style: TextStyle(fontSize: 16),
            ),
            SizedBox(height: 20),
            Text(
              'Registration date:',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              user.resgistrationDate,
              style: TextStyle(fontSize: 16),
            ),
            SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}

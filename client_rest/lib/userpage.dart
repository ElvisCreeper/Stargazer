import 'package:client_rest/main.dart';
import 'package:client_rest/model.dart';
import 'package:client_rest/server_connection.dart';
import 'package:flutter/material.dart';

class UserPage extends StatelessWidget {
  User user;
  int loggedUserId;
  UserPage(this.user, this.loggedUserId);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("User Information"),
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
                            title: const Text("Change user information:"),
                            content: Column(
                              children: [
                                const SizedBox(height: 20,),
                                TextField(
                                  controller: usernameController,
                                  decoration: const InputDecoration(
                                    hintText: 'Edit your username',
                                    labelText: 'Username',
                                    border: OutlineInputBorder(),
                                  ),
                                ),
                                const SizedBox(height: 20,),
                                TextField(
                                  controller: bioController,
                                  decoration: const InputDecoration(
                                    hintText: 'Edit your bio',
                                    labelText: 'Bio',
                                    border: OutlineInputBorder(),
                                  ),
                                ),
                                TextButton(onPressed: (){
                                  updateUser(user.userId, box.get(user.id)!.password , usernameController.text, bioController.text);
                                  user.username = usernameController.text;
                                  user.bio = bioController.text;
                                  box.put(user);
                                  Navigator.pop(context);
                                  Navigator.pop(context);
                                }, child: const Text("Update"))
                              ],
                            ),
                          );
                        },
                      );
                    },
                    icon: const Icon(Icons.edit_square))
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
              child: user.image == null ? const Icon(Icons.person, size: 100,) : null,
            ),
            const SizedBox(
              height: 40,
            ),
            const Text(
              'Username:',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              user.username,
              style: const TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 20),
            const Text(
              'Bio:',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              user.bio ?? "",
              style: const TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 20),
            const Text(
              'Registration date:',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              user.resgistrationDate,
              style: const TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}

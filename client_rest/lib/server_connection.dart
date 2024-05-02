import 'package:client_rest/main.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'model.dart';

String ip = "10.0.2.2";

Future<dynamic> register(String username, String password) async {
  var response = await http.post(
    Uri.http(ip, "/social/api/users.php"),
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    //encoding: Encoding.getByName('utf-8'),
    body: {"username": username, "password": password},
  );
  print(response.statusCode);
  print(response.body);
  switch (response.statusCode) {
    case 201:
      var responseJson = json.decode(response.body);
      userListView.addUser(
          user: User(
              userId: responseJson["id"],
              username: responseJson["Username"],
              password: password,
              resgistrationDate: responseJson["registrationDate"],
              bio: responseJson["Bio"],
              image: responseJson["Image"]));
      break;
    case 401:
      return "Username already taken";
  }
}

Future<dynamic> login(String username, String password) async {
  var response = await http.get(Uri.http(
      ip, "social/api/auth.php", {"username": username, "password": password}));
  switch (response.statusCode) {
    case 200:
      var responseJson = json.decode(response.body);
      userListView.addUser(
          user: User(
              userId: responseJson["id"],
              username: responseJson["Username"],
              password: password,
              resgistrationDate: responseJson["registrationDate"],
              bio: responseJson["Bio"],
              image: responseJson["Image"]));
      break;
    case 401:
      return "Wrong password";
    case 404:
      return "User does not exist";
  }
}


Future<List> getSubjects() async {
   var response = await http.get(Uri.http(
      ip, "social/api/subjects.php"));
  switch (response.statusCode) {
    case 200:
      return  json.decode(response.body);
  }
  return [];
}
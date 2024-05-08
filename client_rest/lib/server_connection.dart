import 'package:client_rest/login.dart';
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

Future<User?> getUser(int userId) async {
  var response = await http
      .get(Uri.http(ip, "social/api/users.php", {"userId": userId.toString()}));
  switch (response.statusCode) {
    case 200:
      var responseJson = json.decode(response.body);
      return User(
          userId: responseJson["id"],
          username: responseJson["Username"],
          password: "",
          resgistrationDate: responseJson["registrationDate"],
          bio: responseJson["Bio"],
          image: responseJson["Image"]);
  }
  return null;
}

updateUser(int userId, String password, String username, String bio) async {
  var response = await http.put(
    Uri.http(ip, "/social/api/users.php").replace(
      queryParameters: {
        "userId": userId.toString(),
        "password": password,
        "Username": username,
        "Bio": bio,
      },
    ),
  );
}

Future<List> getSubjects() async {
  var response = await http.get(Uri.http(ip, "social/api/subjects.php"));
  switch (response.statusCode) {
    case 200:
      return json.decode(response.body);
  }
  return [];
}

Future<List> getTopics(int subjectId) async {
  var response = await http.get(Uri.http(
      ip, "social/api/topics.php", {"subjectId": subjectId.toString()}));
  switch (response.statusCode) {
    case 200:
      return json.decode(response.body);
  }
  return [];
}

Future<List> getTabs(int topicId) async {
  var response = await http.get(
      Uri.http(ip, "social/api/tabs.php", {"topicId": topicId.toString()}));
  switch (response.statusCode) {
    case 200:
      return json.decode(response.body);
  }
  return [];
}

Future<List> getPosts(int tabId, int userId) async {
  var response = await http.get(Uri.http(ip, "social/api/posts.php",
      {"tabId": tabId.toString(), "userId": userId.toString()}));
  switch (response.statusCode) {
    case 200:
      return json.decode(response.body);
  }
  return [];
}

Future<List> getComments(int postId, int userId) async {
  var response = await http.get(Uri.http(ip, "social/api/comments.php",
      {"postId": postId.toString(), "userId": userId.toString()}));
  switch (response.statusCode) {
    case 200:
      return json.decode(response.body);
  }
  return [];
}

ratePost(int userId, String password, String action, int postId) async {
  var response = await http.post(
    Uri.http(ip, "/social/api/likes.php"),
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    //encoding: Encoding.getByName('utf-8'),
    body: {
      "userId": userId.toString(),
      "password": password,
      "action": action,
      "postId": postId.toString()
    },
  );
}

createPost(
    int userId, String password, String title, String text, int tabId) async {
  var response = await http.post(
    Uri.http(ip, "/social/api/posts.php"),
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    //encoding: Encoding.getByName('utf-8'),
    body: {
      "userId": userId.toString(),
      "password": password,
      "title": title,
      "text": text,
      "tabId": tabId.toString()
    },
  );
}

updatePost(
    int userId, String password, String title, String text, int postId) async {
  var response = await http.put(
    Uri.http(ip, "/social/api/posts.php").replace(
      queryParameters: {
        "userId": userId.toString(),
        "password": password,
        "title": title,
        "text": text,
        "postId": postId.toString()
      },
    ),
  );
}

deletePost(int userId, String password, int postId) async {
  var response = await http.delete(
    Uri.http(ip, "/social/api/posts.php").replace(
      queryParameters: {
        "userId": userId.toString(),
        "password": password,
        "postId": postId.toString()
      },
    ),
  );
}

createComment(int userId, String password, String title, String text, int tabId,
    int postId) async {
  var response = await http.post(
    Uri.http(ip, "/social/api/comments.php"),
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    //encoding: Encoding.getByName('utf-8'),
    body: {
      "userId": userId.toString(),
      "password": password,
      "title": title,
      "text": text,
      "tabId": tabId.toString(),
      "replyId": postId.toString()
    },
  );
}

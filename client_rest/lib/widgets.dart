import 'package:client_rest/homepage.dart';
import 'package:client_rest/main.dart';
import 'package:client_rest/model.dart';
import 'package:client_rest/objectbox.g.dart';
import 'package:client_rest/server_connection.dart';
import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

var userListProvider = StateProvider<List<User>>((ref) => box.getAll());

class UserList extends ConsumerWidget {
  var addUser;
  var removeUser;
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final userList = ref.watch(userListProvider);
    addUser = ({required User user}) {
      var query = box.query(User_.userId.equals(user.userId)).build();
      if (!query.find().isNotEmpty) {
        box.put(user);
        ref.watch(userListProvider.notifier).state = box.getAll();
      }
      query.close();
    };
    removeUser = ({required int id}) {
      box.remove(id);
      ref.watch(userListProvider.notifier).state = box.getAll();
    };
    return ListView.builder(
      itemCount: userList.length,
      itemBuilder: (context, index) {
        return Dismissible(
          key: ValueKey<int>(userList[index].id),
          child: ListTile(
            leading: CircleAvatar(
              backgroundImage: NetworkImage(
                  'http://$ip/social/userImages/${userList[index].image}'),
              onBackgroundImageError: (exception, stackTrace) => null,
            ),
            title: Text(userList[index].username),
            onTap: () {
              Navigator.pop(context);
              Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => HomePage(
                      user: userList[index],
                    ),
                  ));
            },
          ),
          onDismissed: (DismissDirection direction) =>
              removeUser(id: userList[index].id),
        );
      },
    );
  }
}

class PostCard extends StatelessWidget {
  final Map post;

  PostCard(this.post);

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: <Widget>[
          ListTile(
              trailing: CircleAvatar(
                backgroundImage: NetworkImage(
                    'http://$ip/social/userImages/${post["image"]}'),
              ),
              title: Text(post["title"] != null ? post["title"] : ""),
              subtitle: Text(
                  post["time"] + (post["edited"] == 1 ? " (edited)" : ""))),
          Expanded(
              child: SingleChildScrollView(
                  child: Html(data: post["body"] != null ? post["body"] : ""))),
          Divider(),
          Row(
            children: <Widget>[
              OutlinedButton(
                onPressed: () {
                  showDialog(
                    context: context,
                    builder: (_) {
                      return AlertDialog(
                          actions: [
                            OutlinedButton(
                                onPressed: () {},
                                child: Icon(Icons.add_comment_rounded)),
                            OutlinedButton(
                                onPressed: () =>
                                    Navigator.pop(context, 'Close'),
                                child: Text("Close")),
                          ],
                          content: Container(
                            height: 3000,
                            width: 3000,
                            child: FutureBuilder(
                              future: getComments(post["id"], 1),
                              builder: (context, snapshot) {
                                if (snapshot.hasData) {
                                  return ListView.builder(
                                    itemCount: snapshot.data!.length,
                                    itemBuilder: (context, index) {
                                      return SizedBox(
                                        height: 450,
                                        child: PostCard(snapshot.data![index]),
                                      );
                                    },
                                  );
                                }
                                return Text("No comments yet...");
                              },
                            ),
                          ));
                    },
                  );
                },
                child: Row(
                  children: [Icon(Icons.add_comment_rounded), Text(" Comment")],
                ),
              ),
              LikeButtons(post["id"], post["positive"])
            ],
          ),
          ListTile(
            subtitle: Text(post["likes"].toString() +
                " likes - " +
                post["comments"].toString() +
                " comments"),
          )
        ],
      ),
    );
  }
}

class LikeButtons extends StatefulWidget {
  int postId;
  bool? liked;
  LikeButtons(this.postId, positive, {super.key}){
    if(positive != null){
      positive == 1 ? liked = true : liked = false;
    }
  }

  @override
  State<LikeButtons> createState() => _LikeButtonsState(liked);
}

class _LikeButtonsState extends State<LikeButtons> {
  List<bool> selectedLike = [false, false];

  _LikeButtonsState(bool? liked){
    if(liked != null){
      selectedLike[0] = liked;
      selectedLike[1] = !liked;
    }
  }

  @override
  Widget build(BuildContext context) {
    return ToggleButtons(
      children: [Icon(Icons.thumb_up_alt), Icon(Icons.thumb_down_alt)],
      isSelected: selectedLike,
      onPressed: (index) {
        setState(() {
          print(selectedLike);
          String action = "unlike";
          for (int i = 0; i < selectedLike.length; i++) {
            i == index ? selectedLike[i] = !selectedLike[i] : selectedLike[i] = false;
          }
          print(selectedLike);
          if(selectedLike[0]){
            action = "like";
          }
          if(selectedLike[1]){
            action = "dislike";
          }
          ratePost(HomePage.of(context).user.userId, HomePage.of(context).user.password, action, widget.postId);
        });
      },
    );
  }
}

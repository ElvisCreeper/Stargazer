import 'package:client_rest/editor.dart';
import 'package:client_rest/homepage.dart';
import 'package:client_rest/main.dart';
import 'package:client_rest/model.dart';
import 'package:client_rest/objectbox.g.dart';
import 'package:client_rest/server_connection.dart';
import 'package:client_rest/userpage.dart';
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
              onBackgroundImageError: (exception, stackTrace) {},
              child: userList[index].image == null
                  ? const Icon(Icons.person)
                  : null,
            ),
            title: Text(userList[index].username),
            onTap: () {
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

class PostCard extends ConsumerWidget {
  final Map post;
  static int tabId = 0, userId = 0;
  static String password = "";

  var likesProvider;

  PostCard(this.post) {
    likesProvider = StateProvider<int>((ref) => post["likes"]);
    positive = post["positive"] == 1
        ? true
        : post["positive"] == 0
            ? false
            : null;
  }
  bool? positive;
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final likes = ref.watch(likesProvider);
    changelike(String action) {
      switch (action) {
        case "like":
          ref.read(likesProvider.notifier).state++;
          if (positive != null) {
            ref.read(likesProvider.notifier).state++;
          }
          positive = true;
          post["positive"] = 1;
          break;
        case "dislike":
          ref.read(likesProvider.notifier).state--;
          if (positive != null) {
            ref.read(likesProvider.notifier).state--;
          }
          positive = false;
          post["positive"] = 0;
          break;
        case "unlike":
          positive!
              ? ref.read(likesProvider.notifier).state--
              : ref.read(likesProvider.notifier).state++;
          positive = null;
          post["positive"] = null;
      }
    }

    return Card(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: <Widget>[
          ListTile(
              trailing: post["UserId"] == userId
                  ? PopupMenuButton(itemBuilder: (context) {
                      return [
                        PopupMenuItem(
                          child: const Row(
                            children: [
                              Icon(Icons.edit),
                              Text(" Edit"),
                            ],
                          ),
                          onTap: () {
                            Navigator.push(
                                context,
                                MaterialPageRoute(
                                    builder: (_) => EditorPage(
                                          post["id"],
                                          userId,
                                          password,
                                          tabId,
                                          "edit",
                                          post["title"] ?? "",
                                          post["body"] ?? "",
                                        )));
                          },
                        ),
                        PopupMenuItem(
                          child: const Row(
                            children: [
                              Icon(
                                Icons.delete,
                                color: Colors.red,
                              ),
                              Text(
                                " Delete",
                                style: TextStyle(color: Colors.red),
                              ),
                            ],
                          ),
                          onTap: () {
                            deletePost(userId, password, post["id"]);
                          },
                        ),
                      ];
                    })
                  : null,
              title: Text(post["title"] ?? ""),
              leading: GestureDetector(
                onTap: () {
                  getUser(post["UserId"]).then(
                    (value) => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => UserPage(value!, userId),
                        )),
                  );
                },
                child: CircleAvatar(
                  backgroundImage: NetworkImage(
                      'http://$ip/social/userImages/${post["image"]}'),
                  child:
                      post["image"] == null ? const Icon(Icons.person) : null,
                ),
              ),
              subtitle: Text(
                  post["time"] ?? "${post["edited"] == 1 ? " (edited)" : ""}")),
          const Divider(),
          Expanded(
              child:
                  SingleChildScrollView(child: Html(data: post["body"] ?? ""))),
          const Divider(),
          Row(
            children: <Widget>[
              const SizedBox(
                width: 10,
              ),
              LikeButtons(post["id"], positive, changelike),
              const SizedBox(
                width: 20,
              ),
              OutlinedButton(
                onPressed: () {
                  showDialog(
                    useRootNavigator: false,
                    context: context,
                    builder: (_) {
                      return AlertDialog(
                          actions: [
                            OutlinedButton(
                                onPressed: () {
                                  Navigator.push(
                                      context,
                                      MaterialPageRoute(
                                          builder: (_) => EditorPage(
                                                post["id"],
                                                userId,
                                                password,
                                                tabId,
                                                "comment",
                                              )));
                                },
                                child: const Icon(Icons.add_comment_rounded)),
                            OutlinedButton(
                                onPressed: () =>
                                    Navigator.pop(context, 'Close'),
                                child: const Text("Close")),
                          ],
                          content: SizedBox(
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
                                return const Text("No comments yet...");
                              },
                            ),
                          ));
                    },
                  );
                },
                child: const Row(
                  children: [Icon(Icons.add_comment_rounded), Text(" Comment")],
                ),
              ),
            ],
          ),
          ListTile(
            subtitle: Text("$likes likes - ${post["comments"]} comments"),
          )
        ],
      ),
    );
  }
}

class LikeButtons extends StatefulWidget {
  int postId;
  bool? liked;
  var changePost;
  LikeButtons(this.postId, positive, this.changePost, {super.key}) {
    if (positive != null) {
      positive ? liked = true : liked = false;
    }
  }

  @override
  State<LikeButtons> createState() => _LikeButtonsState(liked);
}

class _LikeButtonsState extends State<LikeButtons> {
  List<bool> selectedLike = [false, false];

  _LikeButtonsState(bool? liked) {
    if (liked != null) {
      selectedLike[0] = liked;
      selectedLike[1] = !liked;
    }
  }

  @override
  Widget build(BuildContext context) {
    return ToggleButtons(
      borderRadius: BorderRadius.circular(50),
      selectedColor: selectedLike[0] ? Colors.green : Colors.red,
      isSelected: selectedLike,
      onPressed: (index) {
        setState(() {
          String action = "unlike";
          for (int i = 0; i < selectedLike.length; i++) {
            i == index
                ? selectedLike[i] = !selectedLike[i]
                : selectedLike[i] = false;
          }
          if (selectedLike[0]) {
            action = "like";
          }
          if (selectedLike[1]) {
            action = "dislike";
          }
          ratePost(PostCard.userId, PostCard.password, action, widget.postId);
          widget.changePost(action);
        });
      },
      children: const [Icon(Icons.thumb_up_alt), Icon(Icons.thumb_down_alt)],
    );
  }
}

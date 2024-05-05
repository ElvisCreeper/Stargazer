import 'package:client_rest/server_connection.dart';
import 'package:client_rest/solar_system.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'model.dart';
import 'objectbox.g.dart';
import 'package:flutter_html/flutter_html.dart';

// Inizializza ObjectBox
late final Store _store;
late final Box<User> _box;

var userListProvider = StateProvider<List<User>>((ref) => _box.getAll());
var userListView = UserList();

var pathProvider = StateProvider<List<Widget>>((ref) => []);

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  _store = await openStore();
  _box = _store.box<User>();

  runApp(const ProviderScope(child: MainApp()));
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(home: LoginPage());
  }
}

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

class UserList extends ConsumerWidget {
  var addUser;
  var removeUser;
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final userList = ref.watch(userListProvider);
    addUser = ({required User user}) {
      var query = _box.query(User_.userId.equals(user.userId)).build();
      if (!query.find().isNotEmpty) {
        _box.put(user);
        ref.watch(userListProvider.notifier).state = _box.getAll();
      }
      query.close();
    };
    removeUser = ({required int id}) {
      _box.remove(id);
      ref.watch(userListProvider.notifier).state = _box.getAll();
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
              //Navigator.pop(context);
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

class HomePage extends ConsumerWidget {
  User user;

  HomePage({required this.user});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final path = ref.watch(pathProvider);
    return Scaffold(
        appBar: AppBar(
          title: Consumer(
              builder: (context, ref, child) => SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(children: path))),
        ),
        drawer: Drawer(
          child: ListView(
            children: [
              UserAccountsDrawerHeader(
                otherAccountsPictures: [
                  IconButton(
                      onPressed: () {
                        Navigator.pop(context);
                        Navigator.pop(context);
                      },
                      icon: Icon(Icons.logout))
                ],
                accountName: Text(user.username),
                accountEmail: user.bio == null ? null : Text(user.bio!),
                currentAccountPicture: CircleAvatar(
                  backgroundImage: NetworkImage(
                      'http://$ip/social/userImages/${user.image}'),
                  onBackgroundImageError: (exception, stackTrace) => null,
                ),
              ),
              SizedBox(height: 500, child: UserList()),
            ],
          ),
        ),
        body: Content(ref));
  }
}

class Content extends StatefulWidget {
  var ref;
  Content(this.ref);
  @override
  State<StatefulWidget> createState() => ContentState();
}

class ContentState extends State<Content> {
  Widget state = CircularProgressIndicator();
  List<Widget> _path = [];

  ContentState() {
    this.state = subjects();
  }

  Widget subjects() {
    _path.clear();
    _path.add(TextButton(
        child: Text("Universe"),
        onPressed: () => setState(() {
              state = subjects();
            })));
    return FutureBuilder(
      future: getSubjects(),
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                  snapshot.data!.length,
                  (index) => TextButton(
                      onPressed: () {
                        setState(() {
                          state = topics(snapshot.data?[index]["id"],
                              snapshot.data?[index]["Name"]);
                        });
                      },
                      child: Text(snapshot.data?[index]["Name"]))),
            ),
          );
        }
        return CircularProgressIndicator();
      },
    );
  }

  Widget topics(int subjectId, String subjectName) {
    _path.removeRange(1, _path.length);
    _path.add(TextButton(
        child: Text(subjectName),
        onPressed: () => setState(() {
              state = topics(subjectId, subjectName);
            })));
    List<Widget> pathAux = List.from(_path);
    widget.ref.read(pathProvider.notifier).state = pathAux;

    return FutureBuilder(
      future: getTopics(subjectId),
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          print(snapshot.data?[0]);
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                  snapshot.data!.length,
                  (index) => TextButton(
                      onPressed: () {
                        setState(() {
                          state = tabs(snapshot.data?[index]["id"],
                              snapshot.data?[index]["Name"]);
                        });
                      },
                      child: Text(snapshot.data?[index]["Name"]))),
            ),
          );
        }
        return CircularProgressIndicator();
      },
    );
  }

  Widget tabs(int topicId, String topicName) {
    _path.removeRange(2, _path.length);
    _path.add(TextButton(
        child: Text(topicName),
        onPressed: () => setState(() {
              state = tabs(topicId, topicName);
            })));
    List<Widget> pathAux = List.from(_path);
    widget.ref.read(pathProvider.notifier).state = pathAux;
    return FutureBuilder(
      future: getTabs(topicId),
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          print(snapshot.data?[0]);
          return TabsInfo(
            tabs: snapshot.data!,
            topicName: topicName,
            action: goToPostPage,
          );
        }
        return CircularProgressIndicator();
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return state;
  }

  goToPostPage(int tabId, String tabName) {
    setState(() {
      state = posts(tabId, 1, tabName);
    });
  }

  Widget posts(int tabId, int userId, String tabName) {
    _path.removeRange(3, _path.length);
    _path.add(TextButton(
      child: Text(tabName),
      onPressed: () {},
    ));
    List<Widget> pathAux = List.from(_path);
    widget.ref.read(pathProvider.notifier).state = pathAux;
    return FutureBuilder(
      future: getPosts(tabId, userId),
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          return Column(
            children: [
              SizedBox(
                child: PageView.builder(
                  itemCount: snapshot.data?.length,
                  itemBuilder: (context, index) {
                    return PostCard(snapshot.data?[index]);
                  },
                ),
                height: 450,
              ),
              FlutterLogo(),
            ],
          );
        }
        return CircularProgressIndicator();
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
              OutlinedButton(
                onPressed: () {},
                child: Row(
                  children: [
                    Icon(Icons.thumb_up_alt),
                    Text("  Like"),
                  ],
                ),
              ),
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

class LikeButton extends StatefulWidget {
  const LikeButton({super.key});

  @override
  State<LikeButton> createState() => _LikeButtonState();
}

class _LikeButtonState extends State<LikeButton> {
  bool active = false;
  @override
  Widget build(BuildContext context) {
    return const Placeholder();
  }
}

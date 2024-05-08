import 'package:client_rest/editor.dart';
import 'package:client_rest/model.dart';
import 'package:client_rest/server_connection.dart';
import 'package:client_rest/solar_system.dart';
import 'package:client_rest/userpage.dart';
import 'package:client_rest/widgets.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:loop_page_view/loop_page_view.dart';

class HomePage extends InheritedWidget {
  final _pathProvider = StateProvider<List<Widget>>(
      (ref) => [TextButton(onPressed: () {}, child: const Text("Universe"))]);

  final _fabProvider = StateProvider<FloatingActionButton?>((ref) => null);
  final User user;
  HomePage({required this.user}) : super(child: _HomePage());

  static HomePage of(BuildContext context) =>
      context.dependOnInheritedWidgetOfExactType<HomePage>() as HomePage;
  @override
  bool updateShouldNotify(covariant InheritedWidget oldWidget) {
    return false;
  }
}

class _HomePage extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final User user = HomePage.of(context).user;
    final path = ref.watch(HomePage.of(context)._pathProvider);
    final FloatingActionButton? fab =
        ref.watch(HomePage.of(context)._fabProvider);
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
                  decoration:
                      const BoxDecoration(color: Color.fromRGBO(15, 167, 213, 1)),
                  otherAccountsPictures: [
                    IconButton(
                        onPressed: () {
                          Navigator.pop(context);
                          Navigator.pop(context);
                        },
                        icon: const Icon(Icons.logout))
                  ],
                  accountName: Text(user.username),
                  accountEmail: user.bio == null ? null : Text(user.bio!),
                  currentAccountPicture: GestureDetector(
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => UserPage(user, user.userId),
                        )),
                    child: CircleAvatar(
                      backgroundImage: NetworkImage(
                          'http://$ip/social/userImages/${user.image}'),
                      onBackgroundImageError: (exception, stackTrace) {},
                      child: user.image == null
                          ? const Icon(
                              Icons.person,
                              size: 40,
                            )
                          : null,
                    ),
                  )),
              SizedBox(height: 500, child: UserList()),
            ],
          ),
        ),
        body: Content(ref),
        floatingActionButton: fab);
  }
}

class Content extends StatefulWidget {
  var ref;
  Content(this.ref);
  @override
  State<StatefulWidget> createState() => ContentState();
}

class ContentState extends State<Content> {
  Widget state = const CircularProgressIndicator();
  final List<Widget> _path = [];

  ContentState() {
    state = subjects();
  }

  Widget subjects() {
    _path.clear();
    _path.add(TextButton(
        child: const Text("Universe"),
        onPressed: () => setState(() {
              state = subjects();
            })));
    try {
      List<Widget> pathAux = List.from(_path);
      widget.ref.read(HomePage.of(context)._pathProvider.notifier).state =
          pathAux;
      widget.ref.read(HomePage.of(context)._fabProvider.notifier).state = null;
    } catch (e) {}

    return FutureBuilder(
      future: getSubjects(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.done &&
            snapshot.hasData) {
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
        return const Center(child: Text("There is nothing here..."));
      },
    );
  }

  Widget topics(int subjectId, String subjectName) {
    _path.removeRange(1, _path.length);
    _path.add(TextButton(
        child: Text(subjectName),
        onPressed: () => setState(() {
              widget.ref
                  .read(HomePage.of(context)._fabProvider.notifier)
                  .state = null;
              state = topics(subjectId, subjectName);
            })));
    List<Widget> pathAux = List.from(_path);
    widget.ref.read(HomePage.of(context)._pathProvider.notifier).state =
        pathAux;

    return FutureBuilder(
      future: getTopics(subjectId),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.done &&
            snapshot.hasData) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                  snapshot.data!.length,
                  (index) => Column(
                    children: [
                      TextButton(
                          onPressed: () {
                            setState(() {
                              state = tabs(snapshot.data?[index]["id"],
                                  snapshot.data?[index]["Name"]);
                            });
                          },
                          child: Text(snapshot.data?[index]["Name"])),
                        snapshot.data?[index]["Description"] != null ? Text(snapshot.data?[index]["Description"]) : const Text(""),
                    ],
                  )),
            ),
          );
        }
        return const Center(child: Text("There is nothing here..."));
      },
    );
  }

  Widget tabs(int topicId, String topicName) {
    _path.removeRange(2, _path.length);
    _path.add(TextButton(
        child: Text(topicName),
        onPressed: () => setState(() {
              widget.ref
                  .read(HomePage.of(context)._fabProvider.notifier)
                  .state = null;
              state = tabs(topicId, topicName);
            })));
    List<Widget> pathAux = List.from(_path);
    widget.ref.read(HomePage.of(context)._pathProvider.notifier).state =
        pathAux;
    return FutureBuilder(
      future: getTabs(topicId),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.done &&
            snapshot.hasData) {
          return TabsInfo(
            tabs: snapshot.data!,
            topicName: topicName,
            action: goToPostPage,
          );
        }
        return const Center(child: Text("There is nothing here..."));
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return state;
  }

  goToPostPage(int tabId, String tabName) {
    setState(() {
      state = posts(tabId, HomePage.of(context).user.userId, tabName);
    });
  }

  Widget posts(int tabId, int userId, String tabName) {
    _path.removeRange(3, _path.length);
    _path.add(TextButton(
      child: Text(tabName),
      onPressed: () {},
    ));
    List<Widget> pathAux = List.from(_path);
    widget.ref.read(HomePage.of(context)._pathProvider.notifier).state =
        pathAux;
    widget.ref.read(HomePage.of(context)._fabProvider.notifier).state =
        FloatingActionButton(
            shape: const CircleBorder(),
            onPressed: () {
              Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (context) => EditorPage(
                          null,
                          HomePage.of(this.context).user.userId,
                          HomePage.of(this.context).user.password,
                          tabId,
                          "post")));
            },
            child: const Icon(Icons.add));
    PostCard.userId = userId;
    PostCard.tabId = tabId;
    PostCard.password = HomePage.of(context).user.password;
    return FutureBuilder(
      future: getPosts(tabId, userId),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.done &&
            snapshot.hasData) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(20),
                  alignment: Alignment.center,
                  decoration: const BoxDecoration(
                    shape: BoxShape.circle,
                  ),
                  child: SizedBox(
                    height: 400,
                    child: LoopPageView.builder(
                      itemCount: snapshot.data!.length,
                      itemBuilder: (context, index) {
                        return PostCard(snapshot.data?[index]);
                      },
                    ),
                  ),
                ),
              ],
            ),
          );
        }
        return const Center(child: Text("There is nothing here..."));
      },
    );
  }
}

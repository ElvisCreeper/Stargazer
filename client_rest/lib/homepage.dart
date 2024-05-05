import 'package:client_rest/model.dart';
import 'package:client_rest/server_connection.dart';
import 'package:client_rest/solar_system.dart';
import 'package:client_rest/widgets.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

var _pathProvider = StateProvider<List<Widget>>(
    (ref) => [TextButton(onPressed: () {}, child: Text("Universe"))]);

var _fabProvider = StateProvider<FloatingActionButton?>((ref) => null);

class HomePage extends InheritedWidget {
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
    final path = ref.watch(_pathProvider);
    final FloatingActionButton? fab = ref.watch(_fabProvider);
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
    try {
      List<Widget> pathAux = List.from(_path);
      widget.ref.read(_pathProvider.notifier).state = pathAux;
    } catch (e) {}

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
    widget.ref.read(_pathProvider.notifier).state = pathAux;

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
    widget.ref.read(_pathProvider.notifier).state = pathAux;
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
    widget.ref.read(_pathProvider.notifier).state = pathAux;
    widget.ref.read(_fabProvider.notifier).state = FloatingActionButton(onPressed: (){}); 
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

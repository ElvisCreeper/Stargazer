import 'package:client_rest/server_connection.dart';
import 'package:flutter/material.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'model.dart';
import 'objectbox.g.dart';

// Inizializza ObjectBox
late final Store _store;
late final Box<User> _box;

var userListProvider = StateProvider<List<User>>((ref) => _box.getAll());
var userListView = UserList();

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
              _box.remove(userList[index].id),
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

class HomePage extends StatelessWidget {
  User user;

  HomePage({required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Universe'),
      ),
      drawer: Drawer(
        child: ListView(
          children: [
            UserAccountsDrawerHeader(
              accountName: Text(user.username),
              accountEmail: user.bio == null ? null : Text(user.bio!),
              currentAccountPicture: CircleAvatar(
                backgroundImage:
                    NetworkImage('http://$ip/social/userImages/${user.image}'),
                onBackgroundImageError: (exception, stackTrace) => null,
              ),
            ),
            SizedBox(height: 500, child: userListView),
          ],
        ),
      ),
    );
  }
}

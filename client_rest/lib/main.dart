import 'package:client_rest/login.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'model.dart';
import 'objectbox.g.dart';

// Inizializza ObjectBox
late final Store _store;
late final Box<User> box;

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  _store = await openStore();
  box = _store.box<User>();

  runApp(const ProviderScope(child: MainApp()));
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(home: LoginPage());
  }
}
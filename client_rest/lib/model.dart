import 'package:objectbox/objectbox.dart';

@Entity()
class User {
  @Id()
  int id = 0;
  
  User({required this.userId, required this.username, required this.password, required this.resgistrationDate, this.bio, this.image});

  String username;
  final String password;
  final String resgistrationDate;
  String? bio;
  String? image;
  int userId; 
}
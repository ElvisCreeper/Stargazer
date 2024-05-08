import 'dart:math';
import 'package:flutter/material.dart';

class TabsInfo extends InheritedWidget {
  final List tabs;
  final String topicName;
  final Function action;
  TabsInfo({required this.tabs, required this.topicName, required this.action})
      : super(child: const SolarSystem());

  static TabsInfo of(BuildContext context) =>
      context.dependOnInheritedWidgetOfExactType<TabsInfo>() as TabsInfo;
  @override
  bool updateShouldNotify(covariant InheritedWidget oldWidget) {
    return false;
  }
}

class SolarSystem extends StatefulWidget {
  const SolarSystem({super.key});

  @override
  State<SolarSystem> createState() => _SolarSystemBasicState();
}

class _SolarSystemBasicState extends State<SolarSystem>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;

  @override
  void initState() {
    _animationController = AnimationController(
        vsync: this,
        duration: const Duration(seconds: 100),
        upperBound: 2 * pi);
    _animationController.addListener(() {
      setState(() {});
    });

    _animationController.forward();
    _animationController.repeat();
    super.initState();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return InteractiveViewer(
      maxScale: 10,
      minScale: 0.1,
      boundaryMargin: const EdgeInsets.all(2500),
      child: CustomPaint(
        painter: SolarSystemPainter(
            _animationController,
            TabsInfo.of(context).tabs,
            TabsInfo.of(context).topicName,
            TabsInfo.of(context).action),
        child: Container(),
      ),
    );
  }
}

class SolarSystemPainter extends CustomPainter {
  final Animation<double> animation;
  final String topicName;
  final List tabs;
  final Function action;

  SolarSystemPainter(this.animation, this.tabs, this.topicName, this.action);

  var size;

  @override
  bool? hitTest(Offset position) {
    var planetRadius = 35.0;
    var planetOrbitRadius = 200.0;
    double plantetdistance = 0.5;
    double plantespeed = 7;
    var center = Offset(
        size.width / 2 + planetOrbitRadius * cos(animation.value * plantespeed + plantetdistance),
        size.height / 2 + planetOrbitRadius * sin(animation.value * plantespeed + plantetdistance));
    for (int i = 0; i < tabs.length; ++i) {
      if ((position - center).distance <= planetRadius) {
        action(tabs[i]["id"], tabs[i]["Name"]);
      }
      planetOrbitRadius += 100;
      plantetdistance+=0.5;
      plantespeed-=1;
      center = Offset(size.width / 2 + planetOrbitRadius * cos(animation.value * plantespeed + plantetdistance),
          size.height / 2 + planetOrbitRadius * sin(animation.value * plantespeed + plantetdistance));
    }
  }

  @override
  void paint(Canvas canvas, Size size) {
    this.size = size;
    const textStyle = TextStyle(color: Colors.black);
    final TextPainter textPainter = TextPainter(
        text: TextSpan(text: topicName, style: textStyle),
        textAlign: TextAlign.justify,
        textDirection: TextDirection.ltr)
      ..layout(maxWidth: size.width - 12.0 - 12.0);

    //disegna orbite
    final orbitPaint = Paint()
      ..color = Colors.grey.withOpacity(0.5)
      ..style = PaintingStyle.stroke;
    var planetOrbitRadius = 200.0;
    for (int i = 0; i < tabs.length; ++i) {
      canvas.drawCircle(Offset(size.width / 2, size.height / 2),
          planetOrbitRadius, orbitPaint);
      planetOrbitRadius += 100;
    }

    //disegna sole
    final sunPaint = Paint()..color = Colors.yellow;
    canvas.drawCircle(Offset(size.width / 2, size.height / 2), 100, sunPaint);
    textPainter.paint(
        canvas,
        Offset((size.width - textPainter.width) / 2,
            (size.height - textPainter.height) / 2));

    //disegna pianeti
    final planetPaint = Paint()..color = Colors.brown;
    const planetRadius = 35.0;
    planetOrbitRadius = 200.0;
    double plantetdistance = 0.5;
    double plantespeed = 7;
    for (int i = 0; i < tabs.length; ++i) {
      canvas.drawCircle(
          Offset(size.width / 2 + planetOrbitRadius * cos(animation.value * plantespeed +plantetdistance),
              size.height / 2 + planetOrbitRadius * sin(animation.value * plantespeed + plantetdistance)),
          planetRadius,
          planetPaint);
      textPainter.text = TextSpan(text: tabs[i]["Name"], style: textStyle);
      textPainter.layout(maxWidth: size.width - 12.0 - 12.0);
      textPainter.paint(
          canvas,
          Offset(
              (size.width - textPainter.width) / 2 +
                  planetOrbitRadius * cos(animation.value * plantespeed + plantetdistance),
              (size.height - textPainter.height) / 2 +
                  planetOrbitRadius * sin(animation.value * plantespeed + plantetdistance)));
      planetOrbitRadius += 100;
      plantetdistance+=0.5;
      plantespeed-=1;
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) {
    return true;
  }
}

import 'package:flutter/material.dart';
import 'package:flutter_quill/flutter_quill.dart';

class EditorPage extends StatelessWidget {
  var _controller = QuillController.basic();
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("Create a post"),
      ),
      bottomSheet: Container(
        height: 100,
        child: Column(
          children: [
            Divider(),
            TextButton(onPressed: () {}, child: Text("post")),
          ],
        ),
      ),
      body: Column(
        children: [
          QuillToolbar.simple(
            /*padding: const EdgeInsets.all(8),
            iconSize: 20,
            controller: _controller,
            toolBarColor: Colors.grey[200],
            alignment: WrapAlignment.center,
            toolBarConfig: [
              ToolBarStyle.size,
              ToolBarStyle.headerOne,
              ToolBarStyle.headerTwo,
              ToolBarStyle.color,
              ToolBarStyle.background,
              ToolBarStyle.undo,
              ToolBarStyle.redo,
              ToolBarStyle.listOrdered,
              ToolBarStyle.listBullet,
              ToolBarStyle.bold,
              ToolBarStyle.underline,
              ToolBarStyle.italic,
              ToolBarStyle.strike,
              ToolBarStyle.align,
              ToolBarStyle.link
            ],*/
            configurations: QuillSimpleToolbarConfigurations(
              controller: _controller,
              showFontFamily: false,
              showCodeBlock: false,
              showListCheck: false,
              showInlineCode: false,
              showQuote: false,
              showClipboardCopy: false,
              showClipboardCut: false,
              showClipboardPaste: false,
              showSearchButton: false,
              showAlignmentButtons: true,
              showIndent: false,
            ),
          ),
          QuillSingleChildScrollView(
            controller: ScrollController(),
            viewportBuilder: (context, position) {
              return QuillEditor.basic(
                configurations:
                    QuillEditorConfigurations(controller: _controller),

                /*
              text: "<h1>Hello</h1>This is a quill html editor example 😊",
              hintText: 'Hint text goes here',
              controller: _controller,
              isEnabled: true,
              minHeight: 300,
              hintTextAlign: TextAlign.start,
              padding: const EdgeInsets.only(left: 10, top: 5),
              hintTextPadding: EdgeInsets.zero,
              onFocusChanged: (hasFocus) => debugPrint('has focus $hasFocus'),
              onTextChanged: (text) => debugPrint('widget text change $text'),
              onEditorCreated: () => debugPrint('Editor has been loaded'),
              onEditingComplete: (s) => debugPrint('Editing completed $s'),
              onEditorResized: (height) => debugPrint('Editor resized $height'),
              onSelectionChanged: (sel) =>
                  debugPrint('${sel.index},${sel.length}'),
              loadingBuilder: (context) {
                return const Center(
                    child: CircularProgressIndicator(
                  strokeWidth: 0.4,
                ));
              },*/
              );
            },
          ),
        ],
      ),
    );
  }
}

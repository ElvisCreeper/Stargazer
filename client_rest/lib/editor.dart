import 'package:client_rest/server_connection.dart';
import 'package:flutter/material.dart';
import 'package:html_editor_enhanced/html_editor.dart';

class EditorPage extends StatelessWidget {
  int? postId;
  int userId, tabId;
  String password;
  String action, title = "", submitText = "";
  String? postText;

  EditorPage(this.postId, this.userId, this.password, this.tabId, this.action, [String postTitle="", this.postText]) {
    switch (action) {
      case "post":
        title = "Create a post";
        submitText = "POST";
        break;
      case "edit":
        title = "Edit post";
        submitText = "EDIT";
        titleController.text = postTitle;
        break;
      case "comment":
        title = "Create a comment";
        submitText = "REPLY";
        
        break;
    }
  }

  HtmlEditorController controller = HtmlEditorController();
  TextEditingController titleController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(title),
      ),
      bottomSheet: Container(
        height: 100,
        child: Column(
          children: [
            Divider(),
            TextButton(
                onPressed: () {
                  switch (action) {
                    case "post":
                      controller.getText().then((text) => createPost(
                          userId, password, titleController.text, text, tabId));
                      break;
                    case "edit":
                      controller.getText().then((text) => updatePost(
                          userId, password, titleController.text, text, postId!));
                      break;
                    case "comment":
                      controller.getText().then((text) => createComment(
                          userId, password, titleController.text, text, tabId, postId!));
                      break;
                  }
                },
                child: Text(submitText)),
          ],
        ),
      ),
      body: Column(
        children: [
          TextField(
              controller: titleController,
              decoration: InputDecoration(
                hintText: 'Title',
                labelText: 'Title',
                border: OutlineInputBorder(),
              )),
          HtmlEditor(
            htmlToolbarOptions: HtmlToolbarOptions(
              toolbarType: ToolbarType.nativeExpandable,
              defaultToolbarButtons: [
                StyleButtons(),
                FontSettingButtons(fontName: false),
                ColorButtons(),
                OtherButtons(
                    fullscreen: false,
                    codeview: false,
                    help: false,
                    copy: false,
                    paste: false),
                FontButtons(subscript: false, superscript: false),
                ListButtons(listStyles: false),
                ParagraphButtons(
                    increaseIndent: false,
                    decreaseIndent: false,
                    caseConverter: false,
                    lineHeight: false,
                    textDirection: false),
                InsertButtons(
                    picture: false,
                    audio: false,
                    video: false,
                    otherFile: false,
                    table: false,
                    hr: false)
              ],
            ),
            controller: controller, //required
            htmlEditorOptions: HtmlEditorOptions(
              hint: "Your text here...",
              initialText: postText,
            ),
            otherOptions: OtherOptions(
              height: 400,
            ),
          ),
        ],
      ),
    );
  }
}

function formatDoc(cmd, value = null) {
	if (value) {
		document.execCommand(cmd, false, value);
	} else {
		document.execCommand(cmd);
	}
}

function addLink() {
	const url = prompt('Insert url');
	formatDoc('createLink', url);
}


//const content = document.getElementById('content');

/*content.addEventListener('mouseenter', function () {
	const a = content.querySelectorAll('a');
	a.forEach(item=> {
		item.addEventListener('mouseenter', function () {
			content.setAttribute('contenteditable', false);
			item.target = '_blank';
		})
		item.addEventListener('mouseleave', function () {
			content.setAttribute('contenteditable', true);
		})
	})
})*/


const showCode = document.getElementById('show-code');
let active = false;

showCode.addEventListener('click', function () {
	showCode.dataset.active = !active;
	active = !active
	if (active) {
		content.textContent = content.innerHTML;
		content.setAttribute('contenteditable', false);
	} else {
		content.innerHTML = content.textContent;
		content.setAttribute('contenteditable', true);
	}
})

function submit(tabId, replyId, action) {
	content = document.getElementById('content');
	let title = document.getElementById('title').value; //da sanificare
	let text = content.innerHTML; //da non sanificare
	console.log(title);
	console.log(text);


	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementsByClassName("container")[0].innerHTML = this.responseText;
		}
	};
	if (action == "EDIT") {
		console.log(replyId);
		xmlhttp.open("POST", "../database/editPost.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("title=" + title + "&text=" + text + "&postID=" + replyId);
	}
	else {
		xmlhttp.open("POST", "../database/insertPost.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		var commentString = "";
		if (replyId != null) {
			commentString = "&replyID=" + replyId;
		}
		xmlhttp.send("title=" + title + "&text=" + text + "&tab=" + tabId + commentString);
	}
}
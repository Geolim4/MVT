function getSelectedCode() 
{
    var text = "";
    if (window.getSelection) 
	{
		text = window.getSelection().toString();
	} 
	else if (document.selection && document.selection.type != "Control") 
	{
		text = document.selection.createRange().text;
	}
	return text;
}
function selectCode(a, c)
{
	// Get ID of code block
	var e = a.getElementsByTagName(c)[0];

	// Not IE and IE9+
	if (window.getSelection)
	{
		var s = window.getSelection();
		// Safari
		if (s.setBaseAndExtent)
		{
			s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
		}
		// Firefox and Opera
		else
		{
			// workaround for bug # 42885
			if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) == '<BR>')
			{
				e.innerHTML = e.innerHTML + '&nbsp;';
			}

			var r = document.createRange();
			r.selectNodeContents(e);
			s.removeAllRanges();
			s.addRange(r);
		}
	}
	// Some older browsers
	else if (document.getSelection)
	{
		var s = document.getSelection();
		var r = document.createRange();
		r.selectNodeContents(e);
		s.removeAllRanges();
		s.addRange(r);
	}
	// IE
	else if (document.selection)
	{
		var r = document.body.createTextRange();
		r.moveToElementText(e);
		r.select();
	}
}

/**
* Jump to page
*/
function jumpto()
{
	var page = prompt(jump_page, on_page);

	if (page !== null && !isNaN(page) && page == Math.floor(page) && page > 0)
	{
		if (base_url.indexOf('?') == -1)
		{
			document.location.href = base_url + '?start=' + ((page - 1) * per_page);
		}
		else
		{
			document.location.href = base_url.replace(/&amp;/g, '&') + '&start=' + ((page - 1) * per_page);
		}
	}
}

/**
* Set display of page element
* s[-1,0,1] = hide,toggle display,show
*/
function dE(n, s, type)
{
	if (!type)
	{
		type = 'block';
	}

	var e = document.getElementById(n);
	if (!s)
	{
		s = (e.style.display == '') ? -1 : 1;
	}
	e.style.display = (s == 1) ? type : 'none';
}

/**
* Mark/unmark checkboxes
* id = ID of parent container, name = name prefix, state = state [true/false]
*/
function marklist(id, name, state)
{
	var parent = document.getElementById(id);
	if (!parent)
	{
		eval('parent = document.' + id);
	}

	if (!parent)
	{
		return;
	}

	var rb = parent.getElementsByTagName('input');
	
	for (var r = 0; r < rb.length; r++)
	{
		if (rb[r].name.substr(0, name.length) == name)
		{
			rb[r].checked = state;
		}
	}
}

/**
* Find a member
*/
function find_username(url)
{
	popup(url, 760, 570, '_usersearch');
	return false;
}

/**
* Window popup
*/
function popup(url, width, height, name)
{
	if (!name)
	{
		name = '_popup';
	}

	window.open(url.replace(/&amp;/g, '&'), name, 'height=' + height + ',resizable=yes,scrollbars=yes, width=' + width);
	return false;
}

/**
* Hiding/Showing the side menu
*/
function switch_menu()
{
	var menu = document.getElementById('menu');
	var previous_file = document.getElementById('previous_file');
	var next_file = document.getElementById('next_file');
	var main = document.getElementById('main');
	var toggle = document.getElementById('toggle');
	var handle = document.getElementById('toggle-handle');

	switch (menu_state)
	{
		// hide
		case 'shown':
			main.style.width = '94%';
			menu_state = 'hidden';
			menu.style.display = 'none';
			previous_file.style.display = 'block';
			next_file.style.display = 'block';
			toggle.style.width = '20px';
			handle.style.backgroundImage = 'url(style/images/toggle.gif)';
			handle.style.backgroundRepeat = 'no-repeat';

			<!-- IF S_CONTENT_DIRECTION eq 'rtl' -->
				handle.style.backgroundPosition = '0% 50%';
				toggle.style.left = '96%';
			<!-- ELSE -->
				handle.style.backgroundPosition = '100% 50%';
				toggle.style.left = '0';
			<!-- ENDIF -->
			
			if(typeof('expand_all') !== "undefined"){
				expand_all();
			}
		break;

		// show
		case 'hidden':
			main.style.width = '76%';
			menu_state = 'shown';
			menu.style.display = 'block';
			previous_file.style.display = 'none';
			next_file.style.display = 'none';
			toggle.style.width = '5%';
			handle.style.backgroundImage = 'url(style/images/toggle.gif)';
			handle.style.backgroundRepeat = 'no-repeat';

			<!-- IF S_CONTENT_DIRECTION eq 'rtl' -->
				handle.style.backgroundPosition = '100% 50%';
				toggle.style.left = '75%';
			<!-- ELSE -->
				handle.style.backgroundPosition = '0% 50%';
				toggle.style.left = '15%';
			<!-- ENDIF -->
			
			if(typeof('collapse_all') !== "undefined"){
				collapse_all(true);
			}
		break;
	}
}
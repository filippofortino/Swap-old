$(function(){
	
	var filemanager = $('.filemanager'),
		breadcrumbs = $('.breadcrumbs'),
		fileList = filemanager.find('.data');
	
	// Prevent false upload	
	/*window.location.replace("/swap/#Home")*/
	
	// Skick Breadcrumbs to top on scroll
    $(".breadcrumbs").stick_in_parent();
    
    
    // Show search box on keypress
    var searchBox = $('input[type=search]');
    
    Mousetrap.bind(['command+s', 'ctrl+s'], function(e) {
    	searchBox.focus();
		return false;
	});
	Mousetrap.bind('s r c', function() { setTimeout(function() { searchBox.focus(); }, 1); });
	Mousetrap.bind('s e a r c h', function() { setTimeout(function() { searchBox.focus(); }, 1); });
	Mousetrap.bind('c e r c a', function() { setTimeout(function() { searchBox.focus(); }, 1); });
	
	// Allowing checkboxes to act like radio buttons
	$("input:checkbox").on('click', function() {
		var $box = $(this);
		if ($box.is(":checked")) {
	    	var group = "input:checkbox[name='" + $box.attr("name") + "']";
	    	$(group).prop("checked", false);
			$box.prop("checked", true);
	  	} else {
	    	$box.prop("checked", false);
		}
	});
	
	// Add files names to upload list
	$("#file-uploader").change(function() {
		var listName = $('#files-list');
		var fileName = "";
		var files = $(this)[0].files;
		
		$('#upload-form input[type=submit]').css("display", "block");
		
		for(var i = 0; i < files.length; i++) {
		
			
			fileName = files[i].name;
			
			if(fileName.length > 20) {
				fileName = fileName.substring(0, 20)+'...';
			}
			
			var comp_n = $('<li><span>'+(i+1)+':</span> '+fileName+'</li>');
			comp_n.appendTo(listName);
		}
	});
    
    // Hide Swap Logo when search box is visible on mobile devices
    if($(window).width() < 965) {
	    $(".search input[type='search']").focusin(function() {
		   $("div#logo").animate({ opacity: 0 });
		  // $("header p").css("visibility", "hidden");
	    }).focusout(function() {
		    $("div#logo").animate({ opacity: 1 });
		   // $("header p").css("visibility", "visible");
	    });
    }
    
    // Add tooltip to user image profile
    Tippy('#no-profile-trigger', {
		html: '#user-details-tooltip', // or document.querySelector('#my-template-id')
		theme: 'swap',
		arrow: true,
		position: 'bottom',
		hideOnClick: false,
		trigger: 'click',
		offset: '50'
	});
	
	$('.filemanager').on('click', 'span.delete a', function(e) {
		console.log("Delete");
		
		// Get the 'li' element
		parent = $(this).parent().parent().parent();
		
		//If user confirm the action
		if(confirm("Sei sicuro di voler cancellare questo file?")) {
			// Get the adelete link and perform the actual delete
			delete_link = $(this).attr("href");
			
			$.get("/swap/index.php" + delete_link);
			
			parent.animate({ opacity: 0 }, 600, function() {
				$(this).animate({
					width: 0,
					margin: 0,
					padding: 0
				}, 300);
			});
		}
		
		e.preventDefault();
	});


	$.get('scan.php', function(data) {

		var response = [data],
			currentPath = '',
			breadcrumbsUrls = [];

		var folders = [],
			files = [];

		// This event listener monitors changes on the URL. We use it to
		// capture back/forward navigation in the browser.

		$(window).on('hashchange', function(){

			goto(window.location.hash);

			// We are triggering the event. This will execute 
			// this function on page load, so that we show the correct folder:

		}).trigger('hashchange');


		// Hiding and showing the search box

		/*filemanager.find('.search').click(function(){

			var search = $(this);

			search.find('span').hide();
			search.find('input[type=search]').show().focus();

		});*/


		// Listening for keyboard input on the search field.
		// We are using the "input" event which detects cut and paste
		// in addition to keyboard input.

		filemanager.find("input[type='search']").on('input', function(e){

			folders = [];
			files = [];

			var value = this.value.trim();

			if(value.length) {

				filemanager.addClass('searching');

				// Update the hash on every key stroke
				window.location.hash = 'search=' + value.trim();

			}

			else {

				filemanager.removeClass('searching');
				window.location.hash = encodeURIComponent(currentPath);

			}

		}).on('keyup', function(e){

			// Clicking 'ESC' button triggers focusout and cancels the search

			var search = $(this);

			if(e.keyCode == 27) {

				$('input[type=search]').blur();

			}
			
		}).focusout(function(e){

			// Cancel the search

			var search = $(this);

			if(!search.val().trim().length) {

				window.location.hash = encodeURIComponent(currentPath);
				/*search.hide();
				search.parent().find('span').show();*/

			}

		});


		// Clicking on folders

		fileList.on('click', 'li.folders a.folders', function(e){
			e.preventDefault();

// 			var nextDir = $(this).find('a.folders').attr('href');
			var nextDir = $(this).attr('href');

			if(filemanager.hasClass('searching')) {

				// Building the breadcrumbs

				breadcrumbsUrls = generateBreadcrumbs(nextDir);

				filemanager.removeClass('searching');
				/*filemanager.find('input[type=search]').val('').hide();*/
				filemanager.find('span').show();
			}
			else {
				breadcrumbsUrls.push(nextDir);
			}

			window.location.hash = encodeURIComponent(nextDir);
			currentPath = nextDir;
		});


		// Clicking on breadcrumbs

		breadcrumbs.on('click', 'a', function(e){
			e.preventDefault();

			var index = breadcrumbs.find('a').index($(this)),
				nextDir = breadcrumbsUrls[index];

			breadcrumbsUrls.length = Number(index);

			window.location.hash = encodeURIComponent(nextDir);

		});


		// Navigates to the given hash (path)

		function goto(hash) {

			hash = decodeURIComponent(hash).slice(1).split('=');
			
			$('.button').attr("href", hash);
			$('.dir').attr("value", hash);
			
			if (hash.length) {
				var rendered = '';

				// if hash has search in it

				if (hash[0] === 'search') {

					filemanager.addClass('searching');
					rendered = searchData(response, hash[1].toLowerCase());

					if (rendered.length) {
						currentPath = hash[0];
						render(rendered);
					}
					else {
						render(rendered);
					}

				}

				// if hash is some path

				else if (hash[0].trim().length) {

					rendered = searchByPath(hash[0]);

					if (rendered.length) {

						currentPath = hash[0];
						breadcrumbsUrls = generateBreadcrumbs(hash[0]);
						render(rendered);

					}
					else {
						currentPath = hash[0];
						breadcrumbsUrls = generateBreadcrumbs(hash[0]);
						render(rendered);
					}

				}

				// if there is no hash

				else {
					currentPath = data.path;
					breadcrumbsUrls.push(data.path);
					render(searchByPath(data.path));
				}
			}
		}

		// Splits a file path and turns it into clickable breadcrumbs

		function generateBreadcrumbs(nextDir){
			var path = nextDir.split('/').slice(0);
			for(var i=1;i<path.length;i++){
				path[i] = path[i-1]+ '/' +path[i];
			}
			return path;
		}


		// Locates a file by path

		function searchByPath(dir) {
			var path = dir.split('/'),
				demo = response,
				flag = 0;

			for(var i=0;i<path.length;i++){
				for(var j=0;j<demo.length;j++){
					if(demo[j].name === path[i]){
						flag = 1;
						demo = demo[j].items;
						break;
					}
				}
			}

			demo = flag ? demo : [];
			return demo;
		}


		// Recursively search through the file tree

		function searchData(data, searchTerms) {

			data.forEach(function(d){
				if(d.type === 'folder') {

					searchData(d.items,searchTerms);

					if(d.name.toLowerCase().match(searchTerms)) {
						folders.push(d);
					}
				}
				else if(d.type === 'file') {
					if(d.name.toLowerCase().match(searchTerms)) {
						files.push(d);
					}
				}
			});
			return {folders: folders, files: files};
		}


		// Render the HTML for the file manager

		function render(data) {

			var scannedFolders = [],
				scannedFiles = [];

			if(Array.isArray(data)) {

				data.forEach(function (d) {

					if (d.type === 'folder') {
						scannedFolders.push(d);
					}
					else if (d.type === 'file') {
						scannedFiles.push(d);
					}

				});

			}
			else if(typeof data === 'object') {

				scannedFolders = data.folders;
				scannedFiles = data.files;

			}


			// Empty the old result and make the new one

			fileList.empty().hide();

			if(!scannedFolders.length && !scannedFiles.length) {
				filemanager.find('.nothingfound').show();
			}
			else {
				filemanager.find('.nothingfound').hide();
			}

			if(scannedFolders.length) {

				scannedFolders.forEach(function(f) {

					var itemsLength = f.items.length,
						name = escapeHTML(f.name),
						icon = '<span class="icon folder"></span>';

					if(itemsLength) {
						icon = '<span class="icon folder full"></span>';
					}

					if(itemsLength == 1) {
						itemsLength += ' elemento';
					}
					else if(itemsLength > 1) {
						itemsLength += ' elementi';
					}
					else {
						itemsLength = 'Vuoto';
					}

					var folder = $('<li class="folders"><a href="'+ f.path +'" title="'+ f.path +'" class="folders"></a>'+icon+'<span class="name">' + name + '</span> <span class="details">' + itemsLength + '</span><span class="links"> <a href="download.php?file='+encodeURIComponent(f.path)+'" class="downloadfile">Download</a></span></li>');
					
// 					var folder = $('<li class="folders">'+icon+'<span class="name">' + name + '</span> <span class="details">' + itemsLength + '</span><span class="links"><a href="'+f.path+'" title="'+ f.path +'" target="_blank" class="viewfile">Apri</a> <a href="download.php?file='+encodeURIComponent(f.path)+'" class="downloadfile">Download</a></span></li>');
					
					folder.appendTo(fileList);
				});

			}

			if(scannedFiles.length) {

				scannedFiles.forEach(function(f) {

					var fileSize = bytesToSize(f.size),
						name = escapeHTML(f.name),
						fileType = name.split('.'),
						icon = '<span class="icon file"></span>';

					fileType = fileType[fileType.length-1];
					
					function googleView(path) {
						path = 'https://fortelli.it/swap/' + path;
						var link = 'https://docs.google.com/viewerng/viewer?url='+path;
						return link;
						
					}
					
					/***************************
						Image Preview (BETA)
					****************************/
					/** href="'+f.path+'" title="'+ f.path +'" **/
					var on_the_fly = encodeURI('https://fortelli.it/swap/image/thumbnail/100/'+encodeURIComponent(f.path))+'/cache';
					
					if(fileType == "jpg" || fileType == "png" || fileType == "jpeg" || fileType == "gif")
						icon = '<img class="icon img-preview" src="'+on_the_fly+'" alt='+name+'>';
					else
						icon = '<span class="icon file f-'+fileType+'">.'+fileType+'</span>';
					var file = $('<li class="files"><div class="files"><span class="delete"><a href=?delete='+encodeURIComponent(f.path)+'&prev='+window.location.hash.slice(1)+'><i class="fa fa-trash-o"></i></a></span>'+icon+'<span class="name">'+ name +'</span> <span class="details">'+fileSize+'</span><span class="links"><a href="'+f.path+'" title="'+ f.path +'" target="_blank" class="viewfile">Visualizza</a> <a href="download.php?file='+encodeURIComponent(f.path)+'" class="downloadfile">Download</a></span></div></li>');
					file.appendTo(fileList);
				});

			}


			// Generate the breadcrumbs

			var url = '';

			if(filemanager.hasClass('searching')){

				url = '<span>Risultati della ricerca: </span>';
				fileList.removeClass('animated');

			}
			else {

				fileList.addClass('animated');

				breadcrumbsUrls.forEach(function (u, i) {

					var name = u.split('/');

					if (i !== breadcrumbsUrls.length - 1) {
						url += '<a href="'+u+'"><span class="folderName">' + name[name.length-1] + '</span></a> <i class="fa fa-angle-right arrow"></i> ';
					}
					else {
						url += '<span class="folderName">' + name[name.length-1] + '</span>';
					}
					
				});

			}

			breadcrumbs.text('').append(url);


			// Show the generated elements

			fileList.animate({'display':'inline-block'});

		}


		// This function escapes special html characters in names

		function escapeHTML(text) {
			return text.replace(/\&/g,'&amp;').replace(/\</g,'&lt;').replace(/\>/g,'&gt;');
		}


		// Convert file sizes from bytes to human readable units

		function bytesToSize(bytes) {
			var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
			if (bytes == 0) return '0 Bytes';
			var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
			return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
		}
		
		//var pathname = $(location).attr('hash');
		/*$('.button').attr("href", pathname);*/

	});
});

jQuery(document).ready(function($) {
    $('.home .main-header-menu .menu-btn a .humburger').on('click', function(){
        $(this).toggleClass('active');
        $('.home .main-header-menu .menu-item').toggleClass('show');
        $('.home .main-header-menu .subscribe-btn').hide();
    });
    
    /* Open Author info on button click */
	$('.toggle-info').click(function(){
        var author_id = $(this).data("id");
        
		$('.backdrop-'+author_id).animate({'opacity':'.50'}, 300, 'linear').css('display', 'block');
		$('.box-'+author_id).fadeIn();
	});

	/* Click to close lightbox */
	$('.close, .backdrop').click(function(){
		$('.backdrop').animate({'opacity':'0'}, 300, 'linear', function(){
			$('.backdrop').css('display', 'none');
		});
		$('.box').fadeOut();
	});
	
	$('#copy_link').click(function (e) {
       e.preventDefault();
       var copyText = $(this).attr('href');
    
       document.addEventListener('copy', function(e) {
          e.clipboardData.setData('text/plain', copyText);
          e.preventDefault();
       }, true);
    
       document.execCommand('copy');  
       console.log('copied text : ', copyText);
       alert('Copied to clipboard: ' + copyText); 
    });
    
    $("#filter_category").on("change", function(){
        var data_url = $('option:selected', this).data("url");
        
        window.location.href = data_url;
    });
    
    $("#filter_authors").on("change", function(){
        var data_url = $('option:selected', this).data("url");
        
        window.location.href = data_url;
    });

    $(".thumb-img").on('click', function(){
        var url = $(this).data("url");

        window.location.href = url;
    });
});
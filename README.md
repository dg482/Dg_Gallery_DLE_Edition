DG Gallery 1.5.6 DLE
====================


Description
-----------
Module media gallry for CMS DLE (DataLifeEngine) >= 9.3

Installation
------------

* open file "../engine/endine.php" in text editor
after:
        switch ($do) {

adding:
        	case 'gallery':
        		    include_once ROOT_DIR . '/DGGallery/index.php';
                break;

* adding in .htaccess:


        ###################################GALLERY
        RewriteRule ^gallery/.*$ index.php?do=gallery [NC,L]
        RewriteRule ^dgg/ajax/admin/.*$ admin.php?mod=dg_gallery [NC,L]

* open in browser "http://yor_site/installGallery/index.php"


1)../cms/dataset.php
2)../cms/editor.php
3)../cms/drawcontrol.php‬
3)../cms/table.php
4)../cms/delrow.php
5) ../cms/secreg.php
6) ../cms/navigator.php
7) ../common/control_panel.php
8) ../common/index.php 
9) ../common/main_ar.css + ../common/main_en.css
10) ../common/pframe.php 
11) ../common/privileges.php
11) ../common/manage_group_privs.php
11) ../common/search.php
11) ../common/search_sql.php
11) ../common/forgot_password.php
11) ../common/signin.php
13)../db/mysqlcon.php
14)../db/settings.php
15)  ../lang/lang_ar.inc
16)  ../lang/pages_ar.inc

NOTE: We are going to mention this information ONLY ONCE, because it goes on all < .class> files .

This folder contains all php classes (<dbTable>.class.php) for all database tables. 

Class files can beconsidered as a php programattic joint between DB and PHP

Class files let the dataset understand the structure for every database table, to fill its data when making (insert, update, delete, select) operations .

Class files can also contain events triggered by dataset (onStart, onRemoveRow…). 

Each db column goes here as a two dimentional array, and each key is an attribute for that column. 

Attributes that can go inside a given column (if necessary):

'name' : name of the column to search for
'type' : field type. Takes values: varchar, ID, text, file, int
'caption' : another name for the field
'control' : this one determines the control displayed be the editor when adding or editing the object. Values: none, text, fkey, list, date
'options': options of the 'control'=>'list'
wich is displayed as a <select> HTML element. These options can go like this:
'options'=>array('ID1'=>'value1', 'key2'=>'val2', 'emp'=>'employee')
'ftbl' :FK table  + 'fTitle' : FK field + 'fID' : FK ID + 'fFilter':the where clause to filter FKs
'required' : means does not accept empty values. It takes the value of required
'filetypes' : extensions that are only allowed to upload as an attachement. Eg. 'jpg|gif|png' or 'pdf|exe|lrec'
'resize' :  whether to resize an uploaded picture or not 
 'prefix' : file prefix like: 'PROD_' 
 'sizes' : a 2D array like this: 
array('thumb'=>array('p'=>'B', 'w'=>215, 'h'=>125))
'value' : the default value if we insert an empty one
'format' : if 'control'=>'date' then we can format the date
 'withtime' : true|false

So, all of this goes for every and each single class file, and no need to explain those foreach class.

And we will let you with our .class files for now:



17) ../obj/ads.class.php
18) ../obj/album.class.php
19) ../obj/blocked_ip.class.php
19) ../obj/buy_contract.class.php



20) ../obj/category.class.php



21) ../obj/contactus.class.php



22) ../obj/faq.class.php



23) ../obj/groups.class.php



24) ../obj/job.class.php



25) ../obj/maint_contract.class.php



26) ../obj/news.class.php



27) ../obj/picture.class.php



28) ../obj/product.class.php



29) ../obj/service.class.php



31) ../obj/site_config.class.php



31) ../obj/shared_pool.class.php




31) ../obj/user.class.php



33) ../obj/user_group_privs.class.php



34) ../obj/user_groups.class.php



35) ../obj/user_privs.class.php



36) ../obj/video.class.php



Sixth ../pages  folder


The general content for most pages:

Calling the template functions, "header"at first and "footer" at the end.

Each page has a specific Privelige, which with out it the user can not do any of the administrative operations.

It has many cases that vary accourding to the parameter 'v':
case "e": editor. That is Editing & Adding
case "c":card. That is a one element (a product, a service…) (displays the element's picture ,title, description, text, latest update, number of visits …)
case "t": table. That is all rows result of a query (usually all rows of a table).
case "d": delete. Displaying deleting form for a given element.

At the end, displaying the "Related Pages" section foreach page.


38) ../pages/ads.php    (ADVERTISEMENT Page)

39) ../pages/albums.php         (ALBUMS page)

40) ../pages/bconts.php          (BUY CONTRACT Page)

41) ../pages/blocked_ips.php           (BLOCKED IPs Page)

41) ../pages/category.php           (CATEGORIES Page)

42) ../pages/contactus.php               (CONTACT US & ABOUT Pages)

43) ../pages/download.php              (SOFTWARE DOWNLOADS Page)

// by now every thing seems to be routine, we are going to explain non routine thing by now


 
44) ../pages/faq.php                   (Frequently Asked Questions Pages)
 
// we are going to explain ONLY non-routine thing by now
 

45) ../pages/jobs.php                    (JOBS we are offering)

// we are going to explain ONLY non-routine thing by now


46) ../pages/groups.php                      (GROUPS MANAGEMENT)

// we are going to explain ONLY non-routine thing by now


46) ../pages/location.php                      (MAP)


46) ../pages/maillist.php                      (MAIL LIST MANAGEMTENT)

// we are going to explain ONLY non-routine thing by now


46) ../pages/mconts.php                      (MAINTENANCE CONTRACTS)
 
// we are going to explain ONLY non-routine thing by now
 

47) ../pages/news.php                    (NEWS Page)

48) ../pages/our_customers.php                       (OUR CUSTOMERS Page)

49) ../pages/pictures.php

50) ../pages/products.php

51) ../pages/send_form_email.php (manipulation for sending emails [Contactus OR Maillist] )

52) ../pages/services.php                  (OUR SERVICES Page)

53) ../pages/shared_pool.php

53) ../pages/site_configs.php

53) ../pages/users.php

53) ../pages/videos.php

 
Folder : /tpl
 
53) ../tpl/error.php

53) ../tpl/panel.box.php

53) ../tpl/tpl.tpl.inc

 


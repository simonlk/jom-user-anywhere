jom-user-anywhere
=================

Allow a Jomsocial profile to be shown anywhere on a Joomla site.


/* -------------------------------------
	Bugs
-------------------------------------- */
- Product link doesn't work if poduct doesn't have images


/* ------------------------------------
	To Do
--------------------------------------*/
- Show link to relevant episodes


/* -------------------------------------
	Wishlist
--------------------------------------- */
- Include the product price?
- get any episodes of the show that the user was in
- get the user information based on a custom field in k2
- get the user information based on a custom field in VM
- allow module class suffix to be added from backend
- dyanamic title based on which component is displaying the module e.g. Created by, Artwork of, etc.
- show the latest product
-- a random product
-- specific product from the shop
- make the module float as the user scrolls
-- make the float have a little bit of animation up in there
- style it so it looks good
- make the manufacturer page show a horizontal option of this module, but also make it appear in the modal box in the product Extra
- get the thumbnail of the episode
- get user's listings from the gallery
- get users status http://goo.gl/5uJ8r
- get users website
- make location clickable
- get user's online status http://goo.gl/5uJ8r
- include events the user is going to
- include events the user is owner of
- display as vertical or horizontal
- possible to automatically get episode info?


/* --------------------------------------
	Changelog
---------------------------------------- */
2012-08-20
# hiding output of episode links
# Moved to Github
# Added this readme

2012-06-04
+ added config to show/hide latest products
+ If only one item exists, then don't show featured
+ If featured item and latest item have the same title then don't show featured
+ Create config to show/hide featured VM items

2012-02-20
+ link to the user's art

2012-02-15
+ Display image unavailable if no image exsits
+ Display only published products
+ Only return 1 item from the shop. This is controlled in the output.
+ Fix wrong titles being displayed

2012-02-14
+ Stop processing VM functions if no matching manufacturer is found
+ Stop processing VM functions if no matching products are found for the manufacturer

2012-02-05
+ Added products based on matching manufacturer. That took ages!
+ Trim about me info

2012-02-04
+ Items in shop is now plural or not based on number
+ Changed template
+ show products titles

2012-01-30
+ Match the users name with the name of a manufacturer SEF name using all lower case, alphanumberic only and dashes instead of spaces
+ Output the number of products a user has in the shop
+ add link to manufacturer page (used manufacturer product list instead of actual manufacturer page)

2012-01-25
+ get the user information from the owner of a gallery item
+ add link to portfolio page
+ add link to episode. This is based on information within the user's profile which is to be filled out manually by an administrator.
+ send the user a message from the module with a popup http://goo.gl/5uJ8r
+ conditionally show fields based on what information is set (not finished)

2012-01-24
+ add a config to look up a specifc user
+ give options to set source of userid
+ show message if no profile is found

2012-01-24
+ get user location
+ get the user name
+ link back to the user profile page
+ get user avatar at thumbnail size
+ get about me information
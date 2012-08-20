<?php
defined('_JEXEC') or die;

/**************************

Changelog

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

***************************/

/**************************

To do
- Don't show CIYL episode links unless they exist
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

Bugs
- Product link doesn't work if poduct doesn't have images

page stuff maybe
- get the thumbnail of the episode
- get user's listings from the gallery
- get users status http://goo.gl/5uJ8r
-- and could make it appear in one of those speech bubble typography things built in to t3
- get users website
- make location clickable
- get user's online status http://goo.gl/5uJ8r
- include events the user is going to
- include events the user is owner of
- display as vertical or horizontal
- possible to automatically get episode info?

*********************************/

/*******************************
 *
 * To get to the view area to edit the output
 * scroll way down to the bottom of this file
 *
*******************************/

/**
 * Get module params
 *******************************/

$manualUserId = $params->get('anyuser_manual');



/** 
 * Get current page author
 **********************************/

// Grab the user ID of the author from the content component
$cdd_article =& JTable::getInstance('content');
$cdd_article->load(JRequest::getInt('id'));
$galleryUserID = $cdd_article->created_by;



/**
 * Define the user
 *******************************/

// From within config
if (($params->get('profile_source') == 'manual') && isset($manualUserId)){
	$userId = $manualUserId;
}

// From jReviews page
elseif(($params->get('profile_source') == 'gallery') && isset($galleryUserID)){
	$userId = $galleryUserID;
}

// From k2 field

// From VM Manufacturer

// Manually here
else {
	echo '<!-- Profile not found. Check module settings. -->';
	echo '<p>Hmm.. unable to find that user right now.</p>';
	return;
}

/**
 * Check other params 
*******************************/
// show latest items?
$show_latest = $params->get('show_latest');

// show featured items?
$show_featured = $params->get('show_featured');





/**
 * Jomsocial
 ******************************/

// Required paths and files
$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
include_once($jspath.DS.'libraries'.DS.'core.php');
// Support messaging
include_once($jspath.DS.'libraries'.DS.'messaging.php');
$onclick = CMessaging::getPopup($userId);

// Get the user object
$user =& CFactory::getUser($userId);

// Get the username
$userName = $user->getDisplayName();

// Get user current status
$userStatus = $user->getStatus();

// Get the avatar
$avatarUrl = $user->getThumbAvatar();

// Get the profile url
// this is a better way to do it than the way mention on Jomsocial dev http://goo.gl/5uJ8r
$link = JRoute::_('index.php?option=com_community&view=profile&userid='.$userId); 

// Get about me info
$aboutMe = $user->getInfo('FIELD_ABOUTME');

// Get contact info
$phone = $user->getInfo('FIELD_MOBILE');
$country= $user->getInfo('FIELD_COUNTRY');
$state = $user->getInfo('FIELD_STATE');
$city = $user->getInfo('FIELD_CITY');
$website = $user->getInfo('FIELD_WEBSITE');

// Get episode info
$episodeURL = $user->getInfo('EPISODE_URL');
$episodeTitle = $user->getInfo('EPISODE_TITLE');
$episodeAirDate = $user->getInfo('EPISODE_AIR_DATE');



/**
 * jReviews
 *********************************/



/**
 * Virtuemart
 *********************************/
// read here http://goo.gl/VfkGF
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
$config = VmConfig::loadConfig();

// Make username small case 
$vmArtistName = strtolower($userName);

// Replace spaces with dashes and strip alphanumeric chars
$vmArtistName = preg_replace('/[\s\W]+/','-', $vmArtistName);

// Get the manufacturer name that matches the user name
$db =& JFactory::getDBO(); 
$query = "SELECT * FROM #__virtuemart_manufacturers_en_gb WHERE slug = '$vmArtistName';";
$db->setQuery($query);
$vmManufacturer = $db->loadAssocList();

if ($vmManufacturer) {

	// Get manufacturer id. It's 0 because the ID is the first (only) entry in the associated array.
	$vmManufacturerId = $vmManufacturer[0]['virtuemart_manufacturer_id'];

	// Manufacturer Product List Url
	$vmManufacturerUrl = JROUTE::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $vmManufacturerId);

	// Get products that match the manufacturer
	$db =& JFactory::getDBO(); 
	$query = "SELECT virtuemart_product_id FROM #__virtuemart_product_manufacturers WHERE virtuemart_manufacturer_id = '$vmManufacturerId'";
	$db->setQuery($query);
	$productIds = $db->loadResultArray();

	// Turn the product IDs array in to comma sep list
	$productIdsCsv = join(',', $productIds);

	// Return the published products and order by date created
	$db =& JFactory::getDBO();
	$query = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE virtuemart_product_id IN ($productIdsCsv) AND published = '1' ORDER BY virtuemart_product_id desc";
	$db->setQuery($query);
	$productIds = $db->loadResultArray();

	if ($productIds && $show_latest != 'no') {

		// Turn the product IDs array in to comma sep list
		$productIdsCsv = join(',', $productIds);

		// Count number of products
		$productCount = count($productIds);

		// Get product names from product ID list
		$db =& JFactory::getDBO(); 
		$query = "
			SELECT product_name 
			FROM #__virtuemart_products_en_gb 
			WHERE virtuemart_product_id 
			IN ($productIdsCsv) 
			ORDER BY virtuemart_product_id desc
			";
		$db->setQuery($query);
		$products = $db->loadAssocList();

		// Get the image ids for the products (first/main image only)
		$db =& JFactory::getDBO(); 
		$query = "SELECT virtuemart_product_id, virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id IN ($productIdsCsv) AND ordering = '1' ORDER BY virtuemart_product_id desc";
		$db->setQuery($query);
		$productMedia = $db->loadAssocList();

		// Turn the Media IDs array in to comma sep list
		$productMediaCsv = array();
		foreach ($productMedia as $value) {
			$productMediaCsv[] = $value['virtuemart_media_id'];
		};
		$productMediaCsv = join(',', $productMediaCsv);

		// Get the images based on the CSV of media id
		$db =& JFactory::getDBO(); 
		$query = "
			SELECT file_url_thumb, virtuemart_media_id, virtuemart_product_id  
			FROM #__virtuemart_medias
			INNER JOIN #__virtuemart_product_medias
			USING(virtuemart_media_id)
			WHERE #__virtuemart_medias.virtuemart_media_id IN ($productMediaCsv)
			ORDER BY virtuemart_product_id desc";
		$db->setQuery($query);
		$productImages = $db->loadAssocList();

		// Combine the product media to product images arrays
		function merge($array1, $array2)
		    {
		        
		        if(sizeof($array1)>sizeof($array2))
		        {
		            $size = sizeof($array1);
		        }else{
		            $a = $array1;
		            $array1 = $array2;
		            $array2 = $a;
		            
		            $size = sizeof($array1);
		        }
		        
		        $keys2 = array_keys($array2);
		        
		        for($i = 0;$i<$size;$i++)
		        {
		            $array1[$keys2[$i]] = $array1[$keys2[$i]] + $array2[$keys2[$i]];
		        }
		        
		        $array1 = array_filter($array1);
		        return $array1;
		    }

		// Combine arrays based on value
		// source: http://stackoverflow.com/questions/4703769/combine-2-associative-arrays-where-values-match
		function array_extend($a, $b) {
		    foreach($b as $k=>$v) {
		        if( is_array($v) ) {
		            if( !isset($a[$k]) OR isset($v[0])) {
		                $a[$k] = $v;
		            } else {
		                $a[$k] = array_extend($a[$k], $v);
		            }
		        } else {
		            $a[$k] = $v;
		        }
		    }
		    return $a;
		}

		$productMedia = array_extend($productImages, $productMedia);

		// Product Urls
		$productUrls = array();
		foreach ($productMedia as $key => $value) {
			$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$value['virtuemart_product_id']);
			$productUrls[]['product_url'] = $url;
		};

		// Add urls to the array
		$productMedia = merge($productUrls, $productMedia);

		// Combine it all together
		$products = merge($products, $productMedia);
	};

	/**************************************
	 * Featured products
	**************************************/

	// Return the published products and order by date created
	$db =& JFactory::getDBO();
	$query = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE virtuemart_product_id IN ($productIdsCsv) AND published = '1' AND product_special = '1' ORDER BY virtuemart_product_id desc";
	$db->setQuery($query);
	$featProductIds = $db->loadResultArray();

	if ($featProductIds && $show_featured != 'no') {

		// Turn the product IDs array in to comma sep list
		$featProductIdsCsv = join(',', $featProductIds);

		// Count number of products
		$featProductCount = count($featProductIds);

		// Get product names from product ID list
		$db =& JFactory::getDBO(); 
		$query = "
			SELECT product_name 
			FROM #__virtuemart_products_en_gb 
			WHERE virtuemart_product_id 
			IN ($featProductIdsCsv) 
			ORDER BY virtuemart_product_id desc
			";
		$db->setQuery($query);
		$featProducts = $db->loadAssocList();

		// Get the image ids for the featProducts (first/main image only)
		$db =& JFactory::getDBO(); 
		$query = "SELECT virtuemart_product_id, virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id IN ($featProductIdsCsv) AND ordering = '1' ORDER BY virtuemart_product_id desc";
		$db->setQuery($query);
		$featProductMedia = $db->loadAssocList();

		// Turn the Media IDs array in to comma sep list
		$featProductMediaCsv = array();
		foreach ($featProductMedia as $value) {
			$featProductMediaCsv[] = $value['virtuemart_media_id'];
		};
		$featProductMediaCsv = join(',', $featProductMediaCsv);

		// Get the images based on the CSV of media id
		$db =& JFactory::getDBO(); 
		$query = "
			SELECT file_url_thumb, virtuemart_media_id, virtuemart_product_id  
			FROM #__virtuemart_medias
			INNER JOIN #__virtuemart_product_medias
			USING(virtuemart_media_id)
			WHERE #__virtuemart_medias.virtuemart_media_id IN ($productMediaCsv)
			ORDER BY virtuemart_product_id desc";
		$db->setQuery($query);
		$featProductImages = $db->loadAssocList();
		$featProductMedia = array_extend($featProductImages, $featProductMedia);

		// Product Urls
		$featProductUrls = array();
		foreach ($featProductMedia as $key => $value) {
			$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$value['virtuemart_product_id']);
			$featProductUrls[]['product_url'] = $url;
		};

		// Add urls to the array
		$featProductMedia = merge($featProductUrls, $featProductMedia);

		// Combine it all together
		$featProducts = merge($featProducts, $featProductMedia);
	};
};


/**
 * Output some testingcrap
 *****************************/

/*
echo '<pre>';
print_r($vmManufacturer);
echo '</pre>';
*/
/*
echo '<pre>';
print_r($productIds);
echo '</pre>';
*/
/*
echo '<pre>';
print_r($products);
echo '</pre>';
*/



/********************************
 * Variable List

$userName
Display name from Jomsocial

$userId

$link
Links to the user's Jomsocial Profile

$avatarUrl
The url of the user's avatar

$aboutMe
About me text

$userStatus
User's current status

********************************/




/********************************
 *
 * Output the actual module goodness
 * Only edit BELOW this line
 *
 *******************************/
?>

<div class="anyuserwrap">
	<div class="usertopwrap">
		<div class="anyavatar">
			<a href="<?php echo $link; ?>">
				<img src="<?php echo $avatarUrl; ?>" alt="Photo of <?php echo $userName; ?>" />
			</a>
		</div>

		<div class="anyusername">
			<a href="<?php echo $link; ?>"><?php echo $userName; ?></a>
		</div>

		<?php if(isset($state) || isset($city)) : ?>
		<div class="anylocation">
			<?php echo $city; ?><?php if(isset($state) && isset($city)) : ?>, <?php endif; ?><?php echo $state; ?>
		</div>
		<?php endif; ?>

		<div class="clr"></div>
		
		<?php if (isset($userStatus)) : ?>
		<div class="userstatus">
			<?php echo $userStatus; ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="userinfo">
		<div class="anylinks">
			<a href="<?php echo $link; ?>" title="View full profile"><i class="icon-user"></i> Profile</a>
			&nbsp;
			<a onclick="<?php echo $onclick; ?>" href="#" title="Send a private message"><i class="icon-envelope"></i> Message</a>
			&nbsp;
			<a href="/gallery/my-art?user=<?php echo $userId; ?>" title="View other gallery items"><i class="icon-picture"></i> Art</a>
		</div>

		<?php if(isset($aboutMe)) : ?>
		<div class="anyinfo">
			<dl>
				<dt>About Me</dt>
				<dd><?php echo substr($aboutMe, 0, 150); ?>...
				<br />
				<a href="<?php echo $link; ?>" title="View full profile">Full profile &raquo; </a></dd>
			</dl>
		</div>
		<?php endif; ?>

		<?php if(isset($featProducts) && $featProduct['product_name'] != $product['product_name'] && $productCount > 1) : ?>
		<div class="anyinfo">
			<h3>Featured Shop Item</h3>
				<ul class="anyProducts unstyled">
					<?php foreach ($featProducts as $key => $featProduct) { ?>
						<li class="well">
							<a href="<?php echo $featProduct['product_url']; ?>">
								<img src="<?php echo ($featProduct['file_url_thumb'] ? $featProduct['file_url_thumb'] : '/components/com_virtuemart/assets/images/vmgeneral/noimage.gif' ); ?>" />
								<br />
								<div class="productTitle"><?php echo $featProduct['product_name']; ?></div>
							</a>
						</li>
					<?php break; }	?>
				</ul>
		</div>
		<?php endif; ?>

		<?php if(isset($products)) : ?>
		<div class="anyinfo">
			<h3>My Latest Shop Item</h3>
				<ul class="anyProducts unstyled">
					<?php foreach ($products as $key => $product) { ?>
						<li class="well">
							<a href="<?php echo $product['product_url']; ?>">
								<img src="<?php echo ($product['file_url_thumb'] ? $product['file_url_thumb'] : '/components/com_virtuemart/assets/images/vmgeneral/noimage.gif' ); ?>" />
								<br />
								<div class="productTitle"><?php echo $product['product_name']; ?></div>
							</a>
						</li>
					<?php break; }	?>
				</ul>
			<p><?php echo $userName; ?> has <?php echo ($productCount > 1 ) ? $productCount." items" : $productCount." item"; ?> in the shop.</p>
		</div>
		<div class="anylinks">
			<a href="<?php echo $vmManufacturerUrl; ?>"><i class="icon-shopping-cart"></i> View all <?php echo $userName; ?>'s items</a>
		</div>
		<?php endif; ?>

		<?php if(!empty($episodeAirDate) || !empty($episodeTitle)) : ?>
		<div class="anyinfo">
			<h3>Featured in CIYL Episode</h3>
			<dl>
				<?php if(!empty($episodeAirDate)) : ?>
					<dt>Episode Title</dt>
					<dd><?php echo $episodeTitle ;?></dd>
				<?php endif; ?>
				<?php if(!empty($episodeAirDate) || !empty($episodeTitle)) : ?>
					<dt>Original Air Date</dt>
					<dd><?php echo $episodeAirDate; ?></dd>
				<?php endif; ?>
			</dl>
		</div>
		<?php endif; ?>

		<?php if($episodeURL !== ''): ?>
		<div class="anylinks">
			<a href="<?php echo $episodeURL; ?>"><i class="icon-film"></i> Watch Episode</a>
		</div>
		<?php endif;?>
	</div>

</div>


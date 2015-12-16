// This JavaScript Document 
// Created By Evan Olds
// February 2007
// Updated by Will Morrison, 5/2/08
// -fixed bugs seen on visiting.

// initNav initializes the navigation region by applying appropriate styles (via a class name assignment) and hiding sections of the pulldown menu that don't correspond to the active section
function initNav()
{

	var i = 0;
	// Find the unordered list that contains the navigtion links
	var navUL = findNonFeaturedUL();
	// Get all ULs within this one
	var allULs = navUL.getElementsByTagName("ul");
	// We will loop through the array of anchors in this UL
	var alist = navUL.getElementsByTagName("a");
	// Determine how many items are in the list
	var acount = alist.length;

	// Loop through the links until we find the one that corresponds to the current page. It's new style will be applied and it will apply the style to its "parent" links.
	for (i=0; i<acount; i++)
	{
		// If this anchor is in the navigation region, it is a candidate for a style assignment
		if (putAnchorStyle(alist[i], navUL, allULs) == true)
		{			
			break;	
		}
	}

	// Now the styles are applied so we can hide all ULs that do not have any styled anchors or a styled parent anchor
	for (i=0; i<allULs.length; i++)
	{
		var firstAnchor = allULs[i].getElementsByTagName("a")[0];
		var pAnchor = findParentAnchor(firstAnchor, navUL, allULs);
		if (pAnchor.className != "navcurrentpage" && 
			pAnchor.className != "navparentpath")
		{
			allULs[i].style.display = "none";
		}
	}
}

function doesULHaveStyledAnchor(objUL)
{
	var alist = objUL.getElementsByTagName("a");
	var i = 0;

	for (i=0; i<alist.length; i++)
	{
		if (alist[i].className == "navcurrentpage" || 
			alist[i].className == "navparentpath")
		{
			return true;	
		}
	}

	return false;
}

// Given an unordered list that this control resides in, this function returns the immediate parent UL for the control.
function findImmediateParentUL(navUL, cntrl, strControlTagName)
{

	var moreULs = navUL.getElementsByTagName("ul");
	var numULs = moreULs.length;
	var i = 0;
	var j = 0;

	// If there are no child ULs, return "navUL"
	if (numULs == 0)
	{
		return navUL;	
	}

	// Make an array of all the ULs that have this anchor within them
	var evenMoreULs = new Array();
	for (i=0; i<numULs; i++)
	{
		if (IsChild(moreULs[i], cntrl, strControlTagName) == true)
		{
			evenMoreULs[j] = moreULs[i];
			j++;
		}
	}

	// If the anchor is not within any of the ULs, return navUL
	if (evenMoreULs.length == 0)
	{
		return navUL;
	}

	// If this happens, someone has changed things beyond the original intent of this script
	if (evenMoreULs.length > 3)
	{
		//alert("Your navigation directory goes more than 3 levels deep with unordered lists");	
	}

	// Sort the "evenMoreULs" with highest level parents at the bottom of the list and return the top UL in the list
	for (i=0; i<evenMoreULs.length-1; i++)
	{
		for (j=0; j<evenMoreULs.length-1-i; j++)
		{
			// if UL[j+1] < UL[j]... if it's a child, move it up
			if (IsChild(evenMoreULs[j] , evenMoreULs[j+1], "ul") == true)
			{
				var temp = evenMoreULs[j];
				evenMoreULs[j] = evenMoreULs[j+1];
				evenMoreULs[j+1] = temp;
			}
		}
	}
	return evenMoreULs[0];
}

// Finds the first UL in the "nav" div that is not within the 'featured' section
function findNonFeaturedUL()
{
	var i=0;
	var featuredObj = document.getElementById('featured');
	var navObj = document.getElementById('nav');

	if (navObj == null)
	{
		alert("Page Error: 'nav' div not found.");
		return null;
	}
	if (featuredObj == null)
	{
		alert("Developer alert: Your page does not have the 'featured' div tag. "+
			  "Even if you do not have featured links it is recommended that you leave the featured div tag in your page.");
		return navObj.getElementsByTagName("ul")[0];
	}

	// Get the list of ULs within the featured section
	var featuredULs = featuredObj.getElementsByTagName("ul");
	if (featuredULs.length == 0)
	{
		return navObj.getElementsByTagName("ul")[0];
	}

	var ullist = navObj.getElementsByTagName("ul");
	while (ullist[i] === featuredULs[0])
	{
		i++;	
	}
	return ullist[i];
}

// Finds and returns the anchor in the UL above this one
function findParentAnchor(aobj, navUL, allULs)
{
	var i = 0;

	// First find this anchor's parent UL
	var parentUL = findImmediateParentUL(navUL, aobj, "a");

	// If the parentUL is the main navigation UL, then we're top-level and have no parent anchor
	if (parentUL === navUL)
	{
		return null;	
	}

	// Next find the parent of that UL, this is where the parent anchor will reside
	var ppUL = findImmediateParentUL(navUL, parentUL, "ul");

	// Get the index of this anchor within the parent-parent list
	var index = getChildIndex(ppUL, aobj, "a");
	var alist = ppUL.getElementsByTagName("a");
	var acount = alist.length;

	// Iterate upwards until we have an immediate parent of "ppUL"
	index--;
	while (index >= 0)
	{
		if (ppUL == findImmediateParentUL(navUL, alist[index], "a"))
		{
			return alist[index];	
		}
		index--;
	}
	
	return null;
}

function getChildIndex(parentcntrl, childcntrl, strTagName)
{
	var children = parentcntrl.getElementsByTagName(strTagName);
	var i = 0;
	
	for (i=0; i<children.length; i++)
	{
		if (children[i] === childcntrl)
		{
			return i;	
		}
	}
	return -1;
}

function IsChild(parentcntrl, childcntrl, strTagName)
{
	var children = parentcntrl.getElementsByTagName(strTagName);
	var i = 0;
	
	for (i=0; i<children.length; i++)
	{
		if (children[i] === childcntrl)
		{
			return true;	
		}
	}
	return false;
}

// Takes a best guess at whether or not a link string is absolute. Returns true if the link is believed to be absolute, false otherwise (relative link).
function IsLinkAbsolute(LinkString)
{
	var s = LinkString.toLowerCase();

	if (s.indexOf(":") != -1)
	{
		// If the link has a colon in it, it's most likely absolute
		return true;
	}
	else if (s.substring(0,4) == "www.")
	{
		// We will assume that if the first four characters are "www." then it's absolute
		return true;
	}
	return false;
}

function putAnchorStyle(aobj, navUL, allULs)
{
	var i;

	// Retreive the URL of the page
	var currentURL = location.href.toLowerCase();

	// If "navcurrentpage" has been previously defined as a global variable, then it will override the URL of the page
	if (typeof navcurrentpage != 'undefined')
	{
		if (IsLinkAbsolute(navcurrentpage) == false)
		{
			// Append our path to the string
			i = currentURL.lastIndexOf("\\");
			if (i==-1)
			{
				i = currentURL.lastIndexOf("/");
			}
			currentURL = (currentURL.substring(0, i+1) + navcurrentpage).toLowerCase();
		}
		else
		{
			currentURL = navcurrentpage.toLowerCase();
		}
	}

	// Chop off "?" and anything after it
	i = currentURL.lastIndexOf("?");
	if (i != -1)
	{
		currentURL = currentURL.substring(0, i);
	}

	// If its a folder path "www.google.com/" rather than "www.google.com/default.aspx"
	// Then this should look for a default.aspx or index.html
	if (currentURL.lastIndexOf("/") > currentURL.lastIndexOf("."))
	{
		var oldURL = currentURL;
		// if current URL doesn't match, try /default.aspx
		if (aobj.href.toLowerCase() != currentURL)
		{
			currentURL = currentURL + 'default.aspx';
		}
		// if default.aspx doesnt match, try /index.html
		if (aobj.href.toLowerCase() != currentURL)
		{
			currentURL = oldURL;
			// Now try index.html
			currentURL = currentURL + 'index.html';
		}
	}

	if (aobj.href.toLowerCase() == currentURL)
	{
		// First change the class of this anchor object
		aobj.className = "navcurrentpage";
		
		// Next find the "parent anchor" (anchors are not really containers for other anchors so we must work around this)
		panchor = findParentAnchor(aobj, navUL, allULs);
		while (panchor != null)
		{
			panchor.className = "navparentpath";
			panchor = findParentAnchor(panchor, navUL, allULs);
		}

		return true;
	}

	return false;
}
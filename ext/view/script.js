function joinUrlSegments(base, query) {
    let  separatorChar = "?";
    if(base.includes("?")) {
        separatorChar = "&";
    }
    return base + separatorChar + query;
}

function clearViewMode() {
	document.querySelectorAll('.image_info').forEach((element) => {
		element.classList.remove('infomode-view');
	});
}

function updateAttr(selector, attr, value) {
	document.querySelectorAll(selector).forEach(function(e) {
		let current = e.getAttribute(attr);
		let newval = joinUrlSegments(current, value);
		e.setAttribute(attr, newval);
	});
}

document.addEventListener('DOMContentLoaded', () => {
	// find elements with class image_info and set them to view mode
	// (by default, with no js, they are in edit mode - so that no-js
	// users can still edit them)
	document.querySelectorAll('.image_info').forEach((element) => {
		element.classList.add('infomode-view');
	});

	if(document.location.hash.length > 3) {
		var query = document.location.hash.substring(1);

		updateAttr("LINK#prevlink", "href", query);
		updateAttr("LINK#nextlink", "href", query);
		updateAttr("A#prevlink", "href", query);
		updateAttr("A#nextlink", "href", query);
		updateAttr("span#image_delete_form form", "action", query);
	}
});

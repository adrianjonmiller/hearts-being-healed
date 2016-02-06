var Js = window.Js || {};
Js.Behaviors = {};
Js.Views = {};


// Ready function
Js.Ready = function(fn) {
  if (document.readyState != 'loading'){
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}


// HTTP Request
Js._request = function(type, url, cb) {
  var request = new XMLHttpRequest();
  request.open(type, url, true);

  request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
      if( typeof cb === 'function' ) {
        cb(request.responseText);
      }
    } else {
      console.log(request);
    }
  };

  request.onerror = function(errorMsg) {
    console.log(errorMsg);
  };

  request.send();
}


// Loops through the data-behavior creates a instance of a function based the values it finds
Js._init = function (context) {
  if (!context) {
    context = document;
  }


  // Views Loop
  var views = context.querySelectorAll('[data-view]');
  for(i=0; i<views.length; i++) {
    var view = views[i];
    var states = view.getAttribute('data-view').split(' ');
    for(j=0; j<states.length; j++) {
      var state = states[j];
      Js.Views[state] = _view(view);
    }
  }


  // Elements Loop
  var elements = context.querySelectorAll('[data-behavior]');
  for(i=0; i<elements.length; i++) {
    var element = elements[i];
    var behaviors = element.getAttribute('data-behavior').split(' ');
    for(j=0; j<behaviors.length; j++) {
      var behavior = behaviors[j];
      if(!element[behavior]) {
        try {
          if(Js.Behaviors[behavior]) {
            element[behavior] = new Js.Behaviors[behavior](element);
          }
        } catch (e) {
          console.log(e.stack);
        }
      }
    }
  }
}


// View container
function _view(view) {
  view.render = function(d){
    if(d) {
      var range = document.createRange();
      var frag = range.createContextualFragment(d);
      Js._init(frag);

      if(view.hasChildNodes())
        view.innerHTML = "";

      view.appendChild(frag);
    }
  }
  return view;
}


// Returns true if it is a DOM element
function _isElement(o){
  return (
    typeof HTMLElement === "object" ? o instanceof HTMLElement : //DOM2
    o && typeof o === "object" && o !== null && o.nodeType === 1 && typeof o.nodeName==="string"
  );
}


// Runs Init after DOM Ready
Js.Ready(function(){
  var start = +new Date();
  Js._init();
  var end =  +new Date();  // log end timestamp
  var diff = end - start;
  console.log("Scripts executed in " + diff/1000 + " seconds.");
});

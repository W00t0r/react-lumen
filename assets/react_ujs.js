;(function(document, window) {

  window.ReactLumenUJS = {
    CLASS_NAME_ATTR: 'data-react-class',
    PROPS_ATTR: 'data-react-props',

    getElements: function getElements() {
      var finder = function finder(selector) {
        if(typeof jQuery === 'undefined') {
          return document.querySelectorAll(selector);
        }
        else {
          return jQuery(selector);
        }
      };

      return finder('[' + ReactLumenUJS.CLASS_NAME_ATTR + ']');
    },

    mountComponents: function mountComponents() {
      var elements = ReactLumenUJS.getElements();
      var element;
      var reactClass;
      var props;

      var index = function index(obj, i) {
        return obj[i];
      };

      for(var i = 0; i < elements.length; i++) {
        element = elements[i];
        reactClass = element.getAttribute(ReactLumenUJS.CLASS_NAME_ATTR).split('.').reduce(index, window);
        props = JSON.parse(element.getAttribute(ReactLumenUJS.PROPS_ATTR));

        ReactDOM.render(React.createElement(reactClass, props), element);
      }
    },

    unmountComponents: function unmountComponents() {
      var elements = ReactLumenUJS.getElements();

      for(var i = 0; i < elements.length; i++) {
        ReactDOM.unmountComponentAtNode(elements[i]);
      }
    },

    handleEvents: function handleEvents() {
      if (document.readyState == "complete" || document.readyState == "loaded" || document.readyState == "interactive") {
        ReactLumenUJS.mountComponents();
      }
      else {
        document.addEventListener('DOMContentLoaded', ReactLumenUJS.mountComponents);
      }
      window.addEventListener('unload', ReactLumenUJS.unmountComponents);
    }
  };

  ReactLumenUJS.handleEvents();

})(document, window);

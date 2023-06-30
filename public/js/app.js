/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/css/app.css":
/*!*******************************!*\
  !*** ./resources/css/app.css ***!
  \*******************************/
/***/ (() => {

throw new Error("Module build failed (from ./node_modules/mini-css-extract-plugin/dist/loader.js):\nModuleBuildError: Module build failed (from ./node_modules/css-loader/dist/cjs.js):\nError: Can't resolve 'public/storage/media/robotwide.png' in 'D:\\PHPStormWorkspace\\lottozahlengenerator\\resources\\css'\n    at finishWithoutResolve (D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\enhanced-resolve\\lib\\Resolver.js:369:18)\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\enhanced-resolve\\lib\\Resolver.js:461:15\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\enhanced-resolve\\lib\\Resolver.js:519:5\n    at eval (eval at create (D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\tapable\\lib\\HookCodeFactory.js:33:10), <anonymous>:16:1)\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\enhanced-resolve\\lib\\Resolver.js:519:5\n    at eval (eval at create (D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\tapable\\lib\\HookCodeFactory.js:33:10), <anonymous>:27:1)\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\enhanced-resolve\\lib\\DescriptionFilePlugin.js:89:43\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\enhanced-resolve\\lib\\Resolver.js:519:5\n    at eval (eval at create (D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\tapable\\lib\\HookCodeFactory.js:33:10), <anonymous>:15:1)\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\enhanced-resolve\\lib\\Resolver.js:519:5\n    at processResult (D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\webpack\\lib\\NormalModule.js:764:19)\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\webpack\\lib\\NormalModule.js:866:5\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\loader-runner\\lib\\LoaderRunner.js:400:11\n    at D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\loader-runner\\lib\\LoaderRunner.js:252:18\n    at context.callback (D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\loader-runner\\lib\\LoaderRunner.js:124:13)\n    at Object.loader (D:\\PHPStormWorkspace\\lottozahlengenerator\\node_modules\\css-loader\\dist\\index.js:155:5)\n    at process.processTicksAndRejections (node:internal/process/task_queues:95:5)");

/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
window.addEventListener('DOMContentLoaded', function () {
  console.log('DOM fully loaded and parsed');
  var toggleButton = document.getElementById('toggle-button');
  var closeButton = document.getElementById('close-button');
  var settingsOverlay = document.getElementById('settings-overlay');
  toggleButton.addEventListener('click', function () {
    console.log('Toggle Button Clicked');
    if (settingsOverlay.style.width === '0%' || settingsOverlay.style.width === '') {
      settingsOverlay.style.visibility = 'visible';
      settingsOverlay.style.width = '100%';
    } else {
      settingsOverlay.style.width = '0%';
      setTimeout(function () {
        settingsOverlay.style.visibility = 'hidden';
      }, 600); // Timeout in ms should match the transition time in CSS
    }
  });

  closeButton.addEventListener('click', function () {
    console.log('Close Button Clicked');
    if (settingsOverlay.style.width === '100%') {
      settingsOverlay.style.width = '0%';
      setTimeout(function () {
        settingsOverlay.style.visibility = 'hidden';
      }, 600); // Timeout in ms should match the transition time in CSS
    }
  });
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	__webpack_modules__["./resources/js/app.js"](0, {}, __webpack_require__);
/******/ 	// This entry module doesn't tell about it's top-level declarations so it can't be inlined
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./resources/css/app.css"](0, __webpack_exports__, __webpack_require__);
/******/ 	
/******/ })()
;
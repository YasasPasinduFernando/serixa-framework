/**
 * SERIXA client runtime v0.11.2 — progressive enhancement + interactive modules.
 *
 * NO virtual DOM, hydration, routing, or SPA state engine.
 */
(function (global) {
  'use strict';

  var ATTR_PREFIX = 'data-serixa-on-';
  var EVENT_MAP = {
    click: 'click',
    change: 'change',
    input: 'input',
    submit: 'submit',
    focus: 'focus',
    blur: 'blur',
    keydown: 'keydown',
    keyup: 'keyup'
  };

  /** @type {Object.<string, Function>} */
  var handlers = Object.create(null);
  /** @type {Object.<string, Array<Function>>} */
  var plugins = Object.create(null);
  /** @type {Object.<string, {mount: Function, name: string}>} */
  var modules = Object.create(null);
  /** @type {Array<HTMLElement>} */
  var overlayStack = [];

  function register(name, fn) {
    if (typeof name === 'string' && name !== '' && typeof fn === 'function') {
      handlers[name] = fn;
    }
    return Serixa;
  }

  function unregister(name) {
    delete handlers[name];
    return Serixa;
  }

  function get(name) {
    return handlers[name] || null;
  }

  function use(hook, fn) {
    if (typeof hook === 'string' && typeof fn === 'function') {
      if (!plugins[hook]) {
        plugins[hook] = [];
      }
      plugins[hook].push(fn);
    }
    return Serixa;
  }

  function runPlugins(hook, payload) {
    var list = plugins[hook] || [];
    for (var i = 0; i < list.length; i++) {
      try {
        list[i](payload);
      } catch (err) {
        if (global.console && console.error) {
          console.error('[serixa] plugin error', hook, err);
        }
      }
    }
  }

  function module(name, definition) {
    if (typeof name !== 'string' || !definition || typeof definition.mount !== 'function') {
      return Serixa;
    }
    modules[name] = { name: name, mount: definition.mount, api: definition };
    return Serixa;
  }

  function getModule(name) {
    return modules[name] || null;
  }

  function focusable(root) {
    return Array.prototype.slice.call(
      root.querySelectorAll(
        'a[href],button:not([disabled]),textarea:not([disabled]),input:not([disabled]),select:not([disabled]),[tabindex]:not([tabindex="-1"])'
      )
    ).filter(function (el) {
      return !el.hasAttribute('disabled') && el.getAttribute('aria-hidden') !== 'true';
    });
  }

  function setOpen(el, open) {
    el.setAttribute('data-serixa-open', open ? 'true' : 'false');
    if (open) {
      el.removeAttribute('hidden');
    } else {
      el.setAttribute('hidden', 'hidden');
    }
  }

  function setExpanded(trigger, open) {
    if (trigger) {
      trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
  }

  function focusTrigger(trigger) {
    if (trigger && typeof trigger.focus === 'function') {
      trigger.focus();
    }
  }

  function trapFocus(event, container) {
    if (event.key !== 'Tab' || !container) {
      return;
    }
    var nodes = focusable(container);
    if (!nodes.length) {
      event.preventDefault();
      container.focus();
      return;
    }
    var first = nodes[0];
    var last = nodes[nodes.length - 1];
    if (event.shiftKey && global.document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && global.document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  function pushOverlay(el) {
    overlayStack.push(el);
  }

  function popOverlay(el) {
    overlayStack = overlayStack.filter(function (item) {
      return item !== el;
    });
  }

  /* ——— Event metadata (v0.8) ——— */
  function lookupWidgets(root) {
    return Array.prototype.slice.call(
      root.querySelectorAll(
        '[data-serixa-on-click],[data-serixa-on-change],[data-serixa-on-input],[data-serixa-on-submit],[data-serixa-on-focus],[data-serixa-on-blur],[data-serixa-on-keydown],[data-serixa-on-keyup]'
      )
    );
  }

  function bindElement(el) {
    if (el.getAttribute('data-serixa-bound') === '1') {
      return;
    }
    el.setAttribute('data-serixa-bound', '1');
    Object.keys(EVENT_MAP).forEach(function (key) {
      var attr = ATTR_PREFIX + key;
      if (!el.hasAttribute(attr)) {
        return;
      }
      var handlerName = el.getAttribute(attr);
      var domEvent = EVENT_MAP[key];
      el.addEventListener(domEvent, function (event) {
        var fn = handlers[handlerName];
        var detail = { name: handlerName, event: domEvent, element: el, originalEvent: event };
        runPlugins('before:' + domEvent, detail);
        if (typeof fn === 'function') {
          fn(event, el, detail);
        } else {
          el.dispatchEvent(
            new CustomEvent('serixa:' + handlerName, { bubbles: true, cancelable: true, detail: detail })
          );
        }
        runPlugins('after:' + domEvent, detail);
      });
    });
  }

  /* ——— ModalModule ——— */
  module('modal', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-modal]').forEach(function (rootEl) {
        if (rootEl.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        rootEl.setAttribute('data-serixa-module-bound', '1');
        var dialog = rootEl.querySelector('[data-serixa-modal-dialog]');
        var lastFocus = null;

        function open() {
          lastFocus = global.document.activeElement;
          setOpen(rootEl, true);
          pushOverlay(rootEl);
          if (dialog) {
            dialog.focus();
          }
        }

        function close() {
          if (rootEl.getAttribute('data-serixa-dismissible') === 'false') {
            return;
          }
          setOpen(rootEl, false);
          popOverlay(rootEl);
          if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
          }
        }

        rootEl.addEventListener('click', function (e) {
          if (e.target.closest('[data-serixa-close]') || e.target.hasAttribute('data-serixa-modal-backdrop')) {
            close();
          }
        });

        rootEl.addEventListener('keydown', function (e) {
          if (rootEl.getAttribute('data-serixa-open') !== 'true') {
            return;
          }
          if (e.key === 'Escape') {
            e.preventDefault();
            close();
            return;
          }
          trapFocus(e, dialog);
        });

        root.querySelectorAll('[data-serixa-open-modal="' + rootEl.id + '"]').forEach(function (btn) {
          btn.addEventListener('click', function (e) {
            e.preventDefault();
            open();
          });
        });

        rootEl._serixaOpen = open;
        rootEl._serixaClose = close;
      });
    }
  });

  /* ——— DrawerModule ——— */
  module('drawer', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-drawer]').forEach(function (rootEl) {
        if (rootEl.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        rootEl.setAttribute('data-serixa-module-bound', '1');
        var panel = rootEl.querySelector('[data-serixa-drawer-panel]');
        var lastFocus = null;

        function open() {
          lastFocus = global.document.activeElement;
          setOpen(rootEl, true);
          pushOverlay(rootEl);
          if (panel) {
            panel.focus();
          }
        }

        function close() {
          if (rootEl.getAttribute('data-serixa-dismissible') === 'false') {
            return;
          }
          setOpen(rootEl, false);
          popOverlay(rootEl);
          if (lastFocus && lastFocus.focus) {
            lastFocus.focus();
          }
        }

        rootEl.addEventListener('click', function (e) {
          if (e.target.closest('[data-serixa-close]') || e.target.hasAttribute('data-serixa-drawer-backdrop')) {
            close();
          }
        });

        rootEl.addEventListener('keydown', function (e) {
          if (rootEl.getAttribute('data-serixa-open') !== 'true') {
            return;
          }
          if (e.key === 'Escape') {
            e.preventDefault();
            close();
            return;
          }
          trapFocus(e, panel);
        });

        root.querySelectorAll('[data-serixa-open-drawer="' + rootEl.id + '"]').forEach(function (btn) {
          btn.addEventListener('click', function (e) {
            e.preventDefault();
            open();
          });
        });
      });
    }
  });

  /* ——— DropdownModule ——— */
  module('dropdown', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-dropdown]').forEach(function (rootEl) {
        if (rootEl.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        rootEl.setAttribute('data-serixa-module-bound', '1');
        var trigger = rootEl.querySelector('[data-serixa-dropdown-trigger]');
        var panel = rootEl.querySelector('[data-serixa-dropdown-panel]');

        function items() {
          return Array.prototype.slice.call(rootEl.querySelectorAll('[data-serixa-dropdown-item]:not([aria-disabled="true"])'));
        }

        function open() {
          setOpen(rootEl, true);
          if (panel) {
            panel.removeAttribute('hidden');
          }
          setExpanded(trigger, true);
          var list = items();
          if (list[0]) {
            list[0].focus();
          }
        }

        function close() {
          setOpen(rootEl, false);
          if (panel) {
            panel.setAttribute('hidden', 'hidden');
          }
          setExpanded(trigger, false);
        }

        function toggle() {
          if (rootEl.getAttribute('data-serixa-open') === 'true') {
            close();
          } else {
            open();
          }
        }

        if (trigger) {
          trigger.addEventListener('click', function (e) {
            e.preventDefault();
            toggle();
          });
        }

        rootEl.addEventListener('keydown', function (e) {
          if (rootEl.getAttribute('data-serixa-open') !== 'true') {
            if ((e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') && trigger && trigger.contains(e.target)) {
              e.preventDefault();
              open();
            }
            return;
          }
          var list = items();
          var index = list.indexOf(global.document.activeElement);
          if (e.key === 'Escape') {
            e.preventDefault();
            close();
            focusTrigger(trigger);
          } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (list.length) {
              list[(index + 1) % list.length].focus();
            }
          } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (list.length) {
              list[(index - 1 + list.length) % list.length].focus();
            }
          }
        });

        global.document.addEventListener('click', function (e) {
          if (!rootEl.contains(e.target)) {
            close();
          }
        });
      });
    }
  });

  /* ——— PopoverModule ——— */
  module('popover', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-popover]').forEach(function (rootEl) {
        if (rootEl.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        rootEl.setAttribute('data-serixa-module-bound', '1');
        var trigger = rootEl.querySelector('[data-serixa-popover-trigger]');
        var panel = rootEl.querySelector('[data-serixa-popover-panel]');

        function open() {
          setOpen(rootEl, true);
          if (panel) {
            panel.removeAttribute('hidden');
          }
          setExpanded(trigger, true);
        }

        function close() {
          setOpen(rootEl, false);
          if (panel) {
            panel.setAttribute('hidden', 'hidden');
          }
          setExpanded(trigger, false);
        }

        if (trigger) {
          trigger.addEventListener('click', function (e) {
            e.preventDefault();
            if (rootEl.getAttribute('data-serixa-open') === 'true') {
              close();
            } else {
              open();
            }
          });
        }

        rootEl.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            e.preventDefault();
            close();
            focusTrigger(trigger);
          }
        });

        global.document.addEventListener('click', function (e) {
          if (!rootEl.contains(e.target)) {
            close();
          }
        });
      });
    }
  });

  /* ——— TooltipModule ——— */
  module('tooltip', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-tooltip]').forEach(function (rootEl) {
        if (rootEl.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        rootEl.setAttribute('data-serixa-module-bound', '1');
        var trigger = rootEl.querySelector('[data-serixa-tooltip-trigger]');
        var content = rootEl.querySelector('[data-serixa-tooltip-content]');
        if (!trigger || !content) {
          return;
        }

        function show() {
          content.removeAttribute('hidden');
        }

        function hide() {
          content.setAttribute('hidden', 'hidden');
        }

        trigger.addEventListener('mouseenter', show);
        trigger.addEventListener('mouseleave', hide);
        trigger.addEventListener('focusin', show);
        trigger.addEventListener('focusout', hide);
        trigger.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            hide();
          }
        });
      });
    }
  });

  /* ——— TabsModule ——— */
  module('tabs', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-tabs]').forEach(function (rootEl) {
        if (rootEl.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        rootEl.setAttribute('data-serixa-module-bound', '1');
        var tabs = Array.prototype.slice.call(rootEl.querySelectorAll('[data-serixa-tab]'));
        var panels = Array.prototype.slice.call(rootEl.querySelectorAll('[data-serixa-tabpanel]'));

        function activate(index) {
          tabs.forEach(function (tab, i) {
            var selected = i === index;
            tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            tab.setAttribute('tabindex', selected ? '0' : '-1');
            if (panels[i]) {
              if (selected) {
                panels[i].removeAttribute('hidden');
              } else {
                panels[i].setAttribute('hidden', 'hidden');
              }
            }
          });
          tabs[index].focus();
        }

        tabs.forEach(function (tab, index) {
          tab.addEventListener('click', function () {
            activate(index);
          });
          tab.addEventListener('keydown', function (e) {
            var next = index;
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
              next = (index + 1) % tabs.length;
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
              next = (index - 1 + tabs.length) % tabs.length;
            } else if (e.key === 'Home') {
              next = 0;
            } else if (e.key === 'End') {
              next = tabs.length - 1;
            } else {
              return;
            }
            e.preventDefault();
            activate(next);
          });
        });
      });
    }
  });

  /* ——— AccordionModule ——— */
  module('accordion', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-accordion]').forEach(function (rootEl) {
        if (rootEl.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        rootEl.setAttribute('data-serixa-module-bound', '1');
        var multiple = rootEl.getAttribute('data-serixa-multiple') === 'true';

        rootEl.querySelectorAll('[data-serixa-accordion-item]').forEach(function (item) {
          var trigger = item.querySelector('[data-serixa-accordion-trigger]');
          var panel = item.querySelector('[data-serixa-accordion-panel]');
          if (!trigger || !panel) {
            return;
          }

          trigger.addEventListener('click', function () {
            var open = item.getAttribute('data-serixa-open') === 'true';
            if (!multiple) {
              rootEl.querySelectorAll('[data-serixa-accordion-item]').forEach(function (other) {
                if (other === item) {
                  return;
                }
                other.setAttribute('data-serixa-open', 'false');
                var t = other.querySelector('[data-serixa-accordion-trigger]');
                var p = other.querySelector('[data-serixa-accordion-panel]');
                if (t) {
                  t.setAttribute('aria-expanded', 'false');
                }
                if (p) {
                  p.setAttribute('hidden', 'hidden');
                }
              });
            }
            var next = !open;
            item.setAttribute('data-serixa-open', next ? 'true' : 'false');
            trigger.setAttribute('aria-expanded', next ? 'true' : 'false');
            if (next) {
              panel.removeAttribute('hidden');
            } else {
              panel.setAttribute('hidden', 'hidden');
            }
          });
        });
      });
    }
  });

  /* ——— ToastModule ——— */
  module('toast', {
    mount: function (root) {
      root.querySelectorAll('[data-serixa-toast]').forEach(function (el) {
        if (el.getAttribute('data-serixa-module-bound') === '1') {
          return;
        }
        el.setAttribute('data-serixa-module-bound', '1');
      });

      root.querySelectorAll('[data-serixa-show-toast]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          var id = btn.getAttribute('data-serixa-show-toast');
          var toast = id ? global.document.getElementById(id) : null;
          if (toast) {
            setOpen(toast, true);
            global.setTimeout(function () {
              setOpen(toast, false);
            }, 4000);
          }
        });
      });
    }
  });

  /* ——— Alert / Chip dismiss ——— */
  module('dismiss', {
    mount: function (root) {
      root.addEventListener('click', function (e) {
        var alertBtn = e.target.closest('[data-serixa-alert-dismiss]');
        if (alertBtn) {
          var alert = alertBtn.closest('[data-serixa-alert]');
          if (alert) {
            alert.setAttribute('hidden', 'hidden');
          }
        }
        var chipBtn = e.target.closest('[data-serixa-chip-dismiss]');
        if (chipBtn) {
          var chip = chipBtn.closest('[data-serixa-chip]');
          if (chip) {
            chip.setAttribute('hidden', 'hidden');
          }
        }
      });
    }
  });

  function bootModules(root) {
    var markers = {
      modal: '[data-serixa-modal],[data-serixa-open-modal]',
      drawer: '[data-serixa-drawer],[data-serixa-open-drawer]',
      dropdown: '[data-serixa-dropdown]',
      popover: '[data-serixa-popover]',
      tooltip: '[data-serixa-tooltip]',
      tabs: '[data-serixa-tabs]',
      accordion: '[data-serixa-accordion]',
      toast: '[data-serixa-toast],[data-serixa-show-toast]',
      dismiss: '[data-serixa-alert],[data-serixa-chip],[data-serixa-alert-dismiss],[data-serixa-chip-dismiss]'
    };

    Object.keys(modules).forEach(function (name) {
      var selector = markers[name];
      if (selector && !root.querySelector(selector)) {
        return;
      }
      try {
        modules[name].mount(root);
      } catch (err) {
        if (global.console && console.error) {
          console.error('[serixa] module failed', name, err);
        }
      }
    });
  }

  function boot(root) {
    root = root || global.document;
    if (!root || !root.querySelectorAll) {
      return Serixa;
    }
    lookupWidgets(root).forEach(bindElement);
    bootModules(root);
    runPlugins('boot', { root: root, modules: Object.keys(modules) });
    return Serixa;
  }

  var Serixa = {
    version: '0.11.2',
    register: register,
    unregister: unregister,
    get: get,
    use: use,
    module: module,
    getModule: getModule,
    modules: function () {
      return Object.keys(modules);
    },
    boot: boot,
    lookup: lookupWidgets,
    overlayStack: function () {
      return overlayStack.slice();
    }
  };

  global.Serixa = Serixa;

  if (global.document) {
    if (global.document.readyState === 'loading') {
      global.document.addEventListener('DOMContentLoaded', function () {
        boot(global.document);
      });
    } else {
      boot(global.document);
    }
  }
})(typeof window !== 'undefined' ? window : globalThis);

(function(a) {
    if (typeof define === "function" && define.amd) {
        define([ "jquery" ], a);
    } else {
        a(jQuery);
    }
})(function(a) {
    a.ui = a.ui || {};
    var b = a.ui.version = "1.12.1";
    (function() {
        var b, c = Math.max, d = Math.abs, e = /left|center|right/, f = /top|center|bottom/, g = /[\+\-]\d+(\.[\d]+)?%?/, h = /^\w+/, i = /%$/, j = a.fn.pos;
        function k(a, b, c) {
            return [ parseFloat(a[0]) * (i.test(a[0]) ? b / 100 : 1), parseFloat(a[1]) * (i.test(a[1]) ? c / 100 : 1) ];
        }
        function l(b, c) {
            return parseInt(a.css(b, c), 10) || 0;
        }
        function m(b) {
            var c = b[0];
            if (c.nodeType === 9) {
                return {
                    width: b.width(),
                    height: b.height(),
                    offset: {
                        top: 0,
                        left: 0
                    }
                };
            }
            if (a.isWindow(c)) {
                return {
                    width: b.width(),
                    height: b.height(),
                    offset: {
                        top: b.scrollTop(),
                        left: b.scrollLeft()
                    }
                };
            }
            if (c.preventDefault) {
                return {
                    width: 0,
                    height: 0,
                    offset: {
                        top: c.pageY,
                        left: c.pageX
                    }
                };
            }
            return {
                width: b.outerWidth(),
                height: b.outerHeight(),
                offset: b.offset()
            };
        }
        a.pos = {
            scrollbarWidth: function() {
                if (b !== undefined) {
                    return b;
                }
                var c, d, e = a("<div " + "style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'>" + "<div style='height:100px;width:auto;'></div></div>"), f = e.children()[0];
                a("body").append(e);
                c = f.offsetWidth;
                e.css("overflow", "scroll");
                d = f.offsetWidth;
                if (c === d) {
                    d = e[0].clientWidth;
                }
                e.remove();
                return b = c - d;
            },
            getScrollInfo: function(b) {
                var c = b.isWindow || b.isDocument ? "" : b.element.css("overflow-x"), d = b.isWindow || b.isDocument ? "" : b.element.css("overflow-y"), e = c === "scroll" || c === "auto" && b.width < b.element[0].scrollWidth, f = d === "scroll" || d === "auto" && b.height < b.element[0].scrollHeight;
                return {
                    width: f ? a.pos.scrollbarWidth() : 0,
                    height: e ? a.pos.scrollbarWidth() : 0
                };
            },
            getWithinInfo: function(b) {
                var c = a(b || window), d = a.isWindow(c[0]), e = !!c[0] && c[0].nodeType === 9, f = !d && !e;
                return {
                    element: c,
                    isWindow: d,
                    isDocument: e,
                    offset: f ? a(b).offset() : {
                        left: 0,
                        top: 0
                    },
                    scrollLeft: c.scrollLeft(),
                    scrollTop: c.scrollTop(),
                    width: c.outerWidth(),
                    height: c.outerHeight()
                };
            }
        };
        a.fn.pos = function(b) {
            if (!b || !b.of) {
                return j.apply(this, arguments);
            }
            b = a.extend({}, b);
            var i, n, o, p, q, r, s = a(b.of), t = a.pos.getWithinInfo(b.within), u = a.pos.getScrollInfo(t), v = (b.collision || "flip").split(" "), w = {};
            r = m(s);
            if (s[0].preventDefault) {
                b.at = "left top";
            }
            n = r.width;
            o = r.height;
            p = r.offset;
            q = a.extend({}, p);
            a.each([ "my", "at" ], function() {
                var a = (b[this] || "").split(" "), c, d;
                if (a.length === 1) {
                    a = e.test(a[0]) ? a.concat([ "center" ]) : f.test(a[0]) ? [ "center" ].concat(a) : [ "center", "center" ];
                }
                a[0] = e.test(a[0]) ? a[0] : "center";
                a[1] = f.test(a[1]) ? a[1] : "center";
                c = g.exec(a[0]);
                d = g.exec(a[1]);
                w[this] = [ c ? c[0] : 0, d ? d[0] : 0 ];
                b[this] = [ h.exec(a[0])[0], h.exec(a[1])[0] ];
            });
            if (v.length === 1) {
                v[1] = v[0];
            }
            if (b.at[0] === "right") {
                q.left += n;
            } else if (b.at[0] === "center") {
                q.left += n / 2;
            }
            if (b.at[1] === "bottom") {
                q.top += o;
            } else if (b.at[1] === "center") {
                q.top += o / 2;
            }
            i = k(w.at, n, o);
            q.left += i[0];
            q.top += i[1];
            return this.each(function() {
                var e, f, g = a(this), h = g.outerWidth(), j = g.outerHeight(), m = l(this, "marginLeft"), r = l(this, "marginTop"), x = h + m + l(this, "marginRight") + u.width, y = j + r + l(this, "marginBottom") + u.height, z = a.extend({}, q), A = k(w.my, g.outerWidth(), g.outerHeight());
                if (b.my[0] === "right") {
                    z.left -= h;
                } else if (b.my[0] === "center") {
                    z.left -= h / 2;
                }
                if (b.my[1] === "bottom") {
                    z.top -= j;
                } else if (b.my[1] === "center") {
                    z.top -= j / 2;
                }
                z.left += A[0];
                z.top += A[1];
                e = {
                    marginLeft: m,
                    marginTop: r
                };
                a.each([ "left", "top" ], function(c, d) {
                    if (a.ui.pos[v[c]]) {
                        a.ui.pos[v[c]][d](z, {
                            targetWidth: n,
                            targetHeight: o,
                            elemWidth: h,
                            elemHeight: j,
                            collisionPosition: e,
                            collisionWidth: x,
                            collisionHeight: y,
                            offset: [ i[0] + A[0], i[1] + A[1] ],
                            my: b.my,
                            at: b.at,
                            within: t,
                            elem: g
                        });
                    }
                });
                if (b.using) {
                    f = function(a) {
                        var e = p.left - z.left, f = e + n - h, i = p.top - z.top, k = i + o - j, l = {
                            target: {
                                element: s,
                                left: p.left,
                                top: p.top,
                                width: n,
                                height: o
                            },
                            element: {
                                element: g,
                                left: z.left,
                                top: z.top,
                                width: h,
                                height: j
                            },
                            horizontal: f < 0 ? "left" : e > 0 ? "right" : "center",
                            vertical: k < 0 ? "top" : i > 0 ? "bottom" : "middle"
                        };
                        if (n < h && d(e + f) < n) {
                            l.horizontal = "center";
                        }
                        if (o < j && d(i + k) < o) {
                            l.vertical = "middle";
                        }
                        if (c(d(e), d(f)) > c(d(i), d(k))) {
                            l.important = "horizontal";
                        } else {
                            l.important = "vertical";
                        }
                        b.using.call(this, a, l);
                    };
                }
                g.offset(a.extend(z, {
                    using: f
                }));
            });
        };
        a.ui.pos = {
            _trigger: function(a, b, c, d) {
                if (b.elem) {
                    b.elem.trigger({
                        type: c,
                        position: a,
                        positionData: b,
                        triggered: d
                    });
                }
            },
            fit: {
                left: function(b, d) {
                    a.ui.pos._trigger(b, d, "posCollide", "fitLeft");
                    var e = d.within, f = e.isWindow ? e.scrollLeft : e.offset.left, g = e.width, h = b.left - d.collisionPosition.marginLeft, i = f - h, j = h + d.collisionWidth - g - f, k;
                    if (d.collisionWidth > g) {
                        if (i > 0 && j <= 0) {
                            k = b.left + i + d.collisionWidth - g - f;
                            b.left += i - k;
                        } else if (j > 0 && i <= 0) {
                            b.left = f;
                        } else {
                            if (i > j) {
                                b.left = f + g - d.collisionWidth;
                            } else {
                                b.left = f;
                            }
                        }
                    } else if (i > 0) {
                        b.left += i;
                    } else if (j > 0) {
                        b.left -= j;
                    } else {
                        b.left = c(b.left - h, b.left);
                    }
                    a.ui.pos._trigger(b, d, "posCollided", "fitLeft");
                },
                top: function(b, d) {
                    a.ui.pos._trigger(b, d, "posCollide", "fitTop");
                    var e = d.within, f = e.isWindow ? e.scrollTop : e.offset.top, g = d.within.height, h = b.top - d.collisionPosition.marginTop, i = f - h, j = h + d.collisionHeight - g - f, k;
                    if (d.collisionHeight > g) {
                        if (i > 0 && j <= 0) {
                            k = b.top + i + d.collisionHeight - g - f;
                            b.top += i - k;
                        } else if (j > 0 && i <= 0) {
                            b.top = f;
                        } else {
                            if (i > j) {
                                b.top = f + g - d.collisionHeight;
                            } else {
                                b.top = f;
                            }
                        }
                    } else if (i > 0) {
                        b.top += i;
                    } else if (j > 0) {
                        b.top -= j;
                    } else {
                        b.top = c(b.top - h, b.top);
                    }
                    a.ui.pos._trigger(b, d, "posCollided", "fitTop");
                }
            },
            flip: {
                left: function(b, c) {
                    a.ui.pos._trigger(b, c, "posCollide", "flipLeft");
                    var e = c.within, f = e.offset.left + e.scrollLeft, g = e.width, h = e.isWindow ? e.scrollLeft : e.offset.left, i = b.left - c.collisionPosition.marginLeft, j = i - h, k = i + c.collisionWidth - g - h, l = c.my[0] === "left" ? -c.elemWidth : c.my[0] === "right" ? c.elemWidth : 0, m = c.at[0] === "left" ? c.targetWidth : c.at[0] === "right" ? -c.targetWidth : 0, n = -2 * c.offset[0], o, p;
                    if (j < 0) {
                        o = b.left + l + m + n + c.collisionWidth - g - f;
                        if (o < 0 || o < d(j)) {
                            b.left += l + m + n;
                        }
                    } else if (k > 0) {
                        p = b.left - c.collisionPosition.marginLeft + l + m + n - h;
                        if (p > 0 || d(p) < k) {
                            b.left += l + m + n;
                        }
                    }
                    a.ui.pos._trigger(b, c, "posCollided", "flipLeft");
                },
                top: function(b, c) {
                    a.ui.pos._trigger(b, c, "posCollide", "flipTop");
                    var e = c.within, f = e.offset.top + e.scrollTop, g = e.height, h = e.isWindow ? e.scrollTop : e.offset.top, i = b.top - c.collisionPosition.marginTop, j = i - h, k = i + c.collisionHeight - g - h, l = c.my[1] === "top", m = l ? -c.elemHeight : c.my[1] === "bottom" ? c.elemHeight : 0, n = c.at[1] === "top" ? c.targetHeight : c.at[1] === "bottom" ? -c.targetHeight : 0, o = -2 * c.offset[1], p, q;
                    if (j < 0) {
                        q = b.top + m + n + o + c.collisionHeight - g - f;
                        if (q < 0 || q < d(j)) {
                            b.top += m + n + o;
                        }
                    } else if (k > 0) {
                        p = b.top - c.collisionPosition.marginTop + m + n + o - h;
                        if (p > 0 || d(p) < k) {
                            b.top += m + n + o;
                        }
                    }
                    a.ui.pos._trigger(b, c, "posCollided", "flipTop");
                }
            },
            flipfit: {
                left: function() {
                    a.ui.pos.flip.left.apply(this, arguments);
                    a.ui.pos.fit.left.apply(this, arguments);
                },
                top: function() {
                    a.ui.pos.flip.top.apply(this, arguments);
                    a.ui.pos.fit.top.apply(this, arguments);
                }
            }
        };
        (function() {
            var b, c, d, e, f, g = document.getElementsByTagName("body")[0], h = document.createElement("div");
            b = document.createElement(g ? "div" : "body");
            d = {
                visibility: "hidden",
                width: 0,
                height: 0,
                border: 0,
                margin: 0,
                background: "none"
            };
            if (g) {
                a.extend(d, {
                    position: "absolute",
                    left: "-1000px",
                    top: "-1000px"
                });
            }
            for (f in d) {
                b.style[f] = d[f];
            }
            b.appendChild(h);
            c = g || document.documentElement;
            c.insertBefore(b, c.firstChild);
            h.style.cssText = "position: absolute; left: 10.7432222px;";
            e = a(h).offset().left;
            a.support.offsetFractions = e > 10 && e < 11;
            b.innerHTML = "";
            c.removeChild(b);
        })();
    })();
    var c = a.ui.position;
});

(function(a) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define([ "jquery" ], a);
    } else if (window.jQuery && !window.jQuery.fn.iconpicker) {
        a(window.jQuery);
    }
})(function(a) {
    "use strict";
    var b = {
        isEmpty: function(a) {
            return a === false || a === "" || a === null || a === undefined;
        },
        isEmptyObject: function(a) {
            return this.isEmpty(a) === true || a.length === 0;
        },
        isElement: function(b) {
            return a(b).length > 0;
        },
        isString: function(a) {
            return typeof a === "string" || a instanceof String;
        },
        isArray: function(b) {
            return a.isArray(b);
        },
        inArray: function(b, c) {
            return a.inArray(b, c) !== -1;
        },
        throwError: function(a) {
            throw "Font Awesome Icon Picker Exception: " + a;
        }
    };
    var c = function(d, e) {
        this._id = c._idCounter++;
        this.element = a(d).addClass("iconpicker-element");
        this._trigger("iconpickerCreate", {
            iconpickerValue: this.iconpickerValue
        });
        this.options = a.extend({}, c.defaultOptions, this.element.data(), e);
        this.options.templates = a.extend({}, c.defaultOptions.templates, this.options.templates);
        this.options.originalPlacement = this.options.placement;
        this.container = b.isElement(this.options.container) ? a(this.options.container) : false;
        if (this.container === false) {
            if (this.element.is(".dropdown-toggle")) {
                this.container = a("~ .dropdown-menu:first", this.element);
            } else {
                this.container = this.element.is("input,textarea,button,.btn") ? this.element.parent() : this.element;
            }
        }
        this.container.addClass("iconpicker-container");
        if (this.isDropdownMenu()) {
            this.options.placement = "inline";
        }
        this.input = this.element.is("input,textarea") ? this.element.addClass("iconpicker-input") : false;
        if (this.input === false) {
            this.input = this.container.find(this.options.input);
            if (!this.input.is("input,textarea")) {
                this.input = false;
            }
        }
        this.component = this.isDropdownMenu() ? this.container.parent().find(this.options.component) : this.container.find(this.options.component);
        if (this.component.length === 0) {
            this.component = false;
        } else {
            this.component.find("i").addClass("iconpicker-component");
        }
        this._createPopover();
        this._createIconpicker();
        if (this.getAcceptButton().length === 0) {
            this.options.mustAccept = false;
        }
        if (this.isInputGroup()) {
            this.container.parent().append(this.popover);
        } else {
            this.container.append(this.popover);
        }
        this._bindElementEvents();
        this._bindWindowEvents();
        this.update(this.options.selected);
        if (this.isInline()) {
            this.show();
        }
        this._trigger("iconpickerCreated", {
            iconpickerValue: this.iconpickerValue
        });
    };
    c._idCounter = 0;
    c.defaultOptions = {
        title: false,
        selected: false,
        defaultValue: false,
        placement: "bottom",
        collision: "none",
        animation: true,
        hideOnSelect: true,
        showFooter: false,
        searchInFooter: false,
        mustAccept: false,
        selectedCustomClass: "bg-primary",
        icons: [],
        fullClassFormatter: function(a) {
            return a;
        },
        input: "input,.iconpicker-input",
        inputSearch: false,
        container: false,
        component: ".input-group-text,.iconpicker-component",
        templates: {
            popover: '<div class="iconpicker-popover popover popover-with-header"><div class="arrow"></div>' + '<div class="popover-title"></div><div class="popover-content"></div></div>',
            footer: '<div class="popover-footer"></div>',
            buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' + ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
            search: '<input type="search" class="form-control iconpicker-search" placeholder="Cari Ikon" />',
            iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
            iconpickerItem: '<a role="button" href="javascript:;" class="iconpicker-item"><i></i></a>'
        }
    };
    c.batch = function(b, c) {
        var d = Array.prototype.slice.call(arguments, 2);
        return a(b).each(function() {
            var b = a(this).data("iconpicker");
            if (!!b) {
                b[c].apply(b, d);
            }
        });
    };
    c.prototype = {
        constructor: c,
        options: {},
        _id: 0,
        _trigger: function(b, c) {
            c = c || {};
            this.element.trigger(a.extend({
                type: b,
                iconpickerInstance: this
            }, c));
        },
        _createPopover: function() {
            this.popover = a(this.options.templates.popover);
            var c = this.popover.find(".popover-title");
            if (!!this.options.title) {
                c.append(a('<div class="popover-title-text">' + this.options.title + "</div>"));
            }
            if (this.hasSeparatedSearchInput() && !this.options.searchInFooter) {
                c.append(this.options.templates.search);
            } else if (!this.options.title) {
                c.remove();
            }
            if (this.options.showFooter && !b.isEmpty(this.options.templates.footer)) {
                var d = a(this.options.templates.footer);
                if (this.hasSeparatedSearchInput() && this.options.searchInFooter) {
                    d.append(a(this.options.templates.search));
                }
                if (!b.isEmpty(this.options.templates.buttons)) {
                    d.append(a(this.options.templates.buttons));
                }
                this.popover.append(d);
            }
            if (this.options.animation === true) {
                this.popover.addClass("fade");
            }
            return this.popover;
        },
        _createIconpicker: function() {
            var b = this;
            this.iconpicker = a(this.options.templates.iconpicker);
            var c = function(c) {
                var d = a(this);
                if (d.is("i")) {
                    d = d.parent();
                }
                b._trigger("iconpickerSelect", {
                    iconpickerItem: d,
                    iconpickerValue: b.iconpickerValue
                });
                if (b.options.mustAccept === false) {
                    b.update(d.data("iconpickerValue"));
                    b._trigger("iconpickerSelected", {
                        iconpickerItem: this,
                        iconpickerValue: b.iconpickerValue
                    });
                } else {
                    b.update(d.data("iconpickerValue"), true);
                }
                if (b.options.hideOnSelect && b.options.mustAccept === false) {
                    b.hide();
                }
            };
            for (var d in this.options.icons) {
                if (typeof this.options.icons[d].title === "string") {
                    var e = a(this.options.templates.iconpickerItem);
                    e.find("i").addClass(this.options.fullClassFormatter(this.options.icons[d].title));
                    e.data("iconpickerValue", this.options.icons[d].title).on("click.iconpicker", c);
                    this.iconpicker.find(".iconpicker-items").append(e.attr("title", "." + this.options.icons[d].title));
                    if (this.options.icons[d].searchTerms.length > 0) {
                        var f = "";
                        for (var g = 0; g < this.options.icons[d].searchTerms.length; g++) {
                            f = f + this.options.icons[d].searchTerms[g] + " ";
                        }
                        this.iconpicker.find(".iconpicker-items").append(e.attr("data-search-terms", f));
                    }
                }
            }
            this.popover.find(".popover-content").append(this.iconpicker);
            return this.iconpicker;
        },
        _isEventInsideIconpicker: function(b) {
            var c = a(b.target);
            if ((!c.hasClass("iconpicker-element") || c.hasClass("iconpicker-element") && !c.is(this.element)) && c.parents(".iconpicker-popover").length === 0) {
                return false;
            }
            return true;
        },
        _bindElementEvents: function() {
            var c = this;
            this.getSearchInput().on("keyup.iconpicker", function() {
                c.filter(a(this).val().toLowerCase());
            });
            this.getAcceptButton().on("click.iconpicker", function(e) {
                var a = c.iconpicker.find(".iconpicker-selected").get(0);
                c.update(c.iconpickerValue);
                c._trigger("iconpickerSelected", {
                    iconpickerItem: a,
                    iconpickerValue: c.iconpickerValue
                });
                if (!c.isInline()) {
                    c.hide();
                }
            });
            this.getCancelButton().on("click.iconpicker", function() {
                if (!c.isInline()) {
                    c.hide();
                }
            });
            this.element.on("focus.iconpicker", function(a) {
                c.show();
                a.stopPropagation();
            });
            if (this.hasComponent()) {
                this.component.on("click.iconpicker", function() {
                    c.toggle();
                });
            }
            if (this.hasInput()) {
                this.input.on("keyup.iconpicker", function(d) {
                    if (!b.inArray(d.keyCode, [ 38, 40, 37, 39, 16, 17, 18, 9, 8, 91, 93, 20, 46, 186, 190, 46, 78, 188, 44, 86 ])) {
                        c.update();
                    } else {
                        c._updateFormGroupStatus(c.getValid(this.value) !== false);
                    }
                    if (c.options.inputSearch === true) {
                        c.filter(a(this).val().toLowerCase());
                    }
                });
            }
        },
        _bindWindowEvents: function() {
            var b = a(window.document);
            var c = this;
            var d = ".iconpicker.inst" + this._id;
            a(window).on("resize.iconpicker" + d + " orientationchange.iconpicker" + d, function(a) {
                if (c.popover.hasClass("in")) {
                    c.updatePlacement();
                }
            });
            if (!c.isInline()) {
                b.on("mouseup" + d, function(a) {
                    if (!c._isEventInsideIconpicker(a) && !c.isInline()) {
                        c.hide();
                    }
                });
            }
        },
        _unbindElementEvents: function() {
            this.popover.off(".iconpicker");
            this.element.off(".iconpicker");
            if (this.hasInput()) {
                this.input.off(".iconpicker");
            }
            if (this.hasComponent()) {
                this.component.off(".iconpicker");
            }
            if (this.hasContainer()) {
                this.container.off(".iconpicker");
            }
        },
        _unbindWindowEvents: function() {
            a(window).off(".iconpicker.inst" + this._id);
            a(window.document).off(".iconpicker.inst" + this._id);
        },
        updatePlacement: function(b, c) {
            b = b || this.options.placement;
            this.options.placement = b;
            c = c || this.options.collision;
            c = c === true ? "flip" : c;
            var d = {
                at: "right bottom",
                my: "right top",
                of: this.hasInput() && !this.isInputGroup() ? this.input : this.container,
                collision: c === true ? "flip" : c,
                within: window
            };
            this.popover.removeClass("inline topLeftCorner topLeft top topRight topRightCorner " + "rightTop right rightBottom bottomRight bottomRightCorner " + "bottom bottomLeft bottomLeftCorner leftBottom left leftTop");
            if (typeof b === "object") {
                return this.popover.pos(a.extend({}, d, b));
            }
            switch (b) {
              case "inline":
                {
                    d = false;
                }
                break;

              case "topLeftCorner":
                {
                    d.my = "right bottom";
                    d.at = "left top";
                }
                break;

              case "topLeft":
                {
                    d.my = "left bottom";
                    d.at = "left top";
                }
                break;

              case "top":
                {
                    d.my = "center bottom";
                    d.at = "center top";
                }
                break;

              case "topRight":
                {
                    d.my = "right bottom";
                    d.at = "right top";
                }
                break;

              case "topRightCorner":
                {
                    d.my = "left bottom";
                    d.at = "right top";
                }
                break;

              case "rightTop":
                {
                    d.my = "left bottom";
                    d.at = "right center";
                }
                break;

              case "right":
                {
                    d.my = "left center";
                    d.at = "right center";
                }
                break;

              case "rightBottom":
                {
                    d.my = "left top";
                    d.at = "right center";
                }
                break;

              case "bottomRightCorner":
                {
                    d.my = "left top";
                    d.at = "right bottom";
                }
                break;

              case "bottomRight":
                {
                    d.my = "right top";
                    d.at = "right bottom";
                }
                break;

              case "bottom":
                {
                    d.my = "center top";
                    d.at = "center bottom";
                }
                break;

              case "bottomLeft":
                {
                    d.my = "left top";
                    d.at = "left bottom";
                }
                break;

              case "bottomLeftCorner":
                {
                    d.my = "right top";
                    d.at = "left bottom";
                }
                break;

              case "leftBottom":
                {
                    d.my = "right top";
                    d.at = "left center";
                }
                break;

              case "left":
                {
                    d.my = "right center";
                    d.at = "left center";
                }
                break;

              case "leftTop":
                {
                    d.my = "right bottom";
                    d.at = "left center";
                }
                break;

              default:
                {
                    return false;
                }
                break;
            }
            this.popover.css({
                display: this.options.placement === "inline" ? "" : "block"
            });
            if (d !== false) {
                this.popover.pos(d).css("maxWidth", a(window).width() - this.container.offset().left - 5);
            } else {
                this.popover.css({
                    top: "auto",
                    right: "auto",
                    bottom: "auto",
                    left: "auto",
                    maxWidth: "none"
                });
            }
            this.popover.addClass(this.options.placement);
            return true;
        },
        _updateComponents: function() {
            this.iconpicker.find(".iconpicker-item.iconpicker-selected").removeClass("iconpicker-selected " + this.options.selectedCustomClass);
            if (this.iconpickerValue) {
                this.iconpicker.find("." + this.options.fullClassFormatter(this.iconpickerValue).replace(/ /g, ".")).parent().addClass("iconpicker-selected " + this.options.selectedCustomClass);
            }
            if (this.hasComponent()) {
                var a = this.component.find("i");
                if (a.length > 0) {
                    a.attr("class", this.options.fullClassFormatter(this.iconpickerValue));
                } else {
                    this.component.html(this.getHtml());
                }
            }
        },
        _updateFormGroupStatus: function(a) {
            if (this.hasInput()) {
                if (a !== false) {
                    this.input.parents(".form-group:first").removeClass("has-error");
                } else {
                    this.input.parents(".form-group:first").addClass("has-error");
                }
                return true;
            }
            return false;
        },
        getValid: function(c) {
            if (!b.isString(c)) {
                c = "";
            }
            var d = c === "";
            c = a.trim(c);
            var e = false;
            for (var f = 0; f < this.options.icons.length; f++) {
                if (this.options.icons[f].title === c) {
                    e = true;
                    break;
                }
            }
            if (e || d) {
                return c;
            }
            return false;
        },
        setValue: function(a) {
            var b = this.getValid(a);
            if (b !== false) {
                this.iconpickerValue = b;
                this._trigger("iconpickerSetValue", {
                    iconpickerValue: b
                });
                return this.iconpickerValue;
            } else {
                this._trigger("iconpickerInvalid", {
                    iconpickerValue: a
                });
                return false;
            }
        },
        getHtml: function() {
            return '<i class="' + this.options.fullClassFormatter(this.iconpickerValue) + '"></i>';
        },
        setSourceValue: function(a) {
            a = this.setValue(a);
            if (a !== false && a !== "") {
                if (this.hasInput()) {
                    this.input.val(this.iconpickerValue);
                } else {
                    this.element.data("iconpickerValue", this.iconpickerValue);
                }
                this._trigger("iconpickerSetSourceValue", {
                    iconpickerValue: a
                });
            }
            return a;
        },
        getSourceValue: function(a) {
            a = a || this.options.defaultValue;
            var b = a;
            if (this.hasInput()) {
                b = this.input.val();
            } else {
                b = this.element.data("iconpickerValue");
            }
            if (b === undefined || b === "" || b === null || b === false) {
                b = a;
            }
            return b;
        },
        hasInput: function() {
            return this.input !== false;
        },
        isInputSearch: function() {
            return this.hasInput() && this.options.inputSearch === true;
        },
        isInputGroup: function() {
            return this.container.is(".input-group");
        },
        isDropdownMenu: function() {
            return this.container.is(".dropdown-menu");
        },
        hasSeparatedSearchInput: function() {
            return this.options.templates.search !== false && !this.isInputSearch();
        },
        hasComponent: function() {
            return this.component !== false;
        },
        hasContainer: function() {
            return this.container !== false;
        },
        getAcceptButton: function() {
            return this.popover.find(".iconpicker-btn-accept");
        },
        getCancelButton: function() {
            return this.popover.find(".iconpicker-btn-cancel");
        },
        getSearchInput: function() {
            return this.popover.find(".iconpicker-search");
        },
        filter: function(c) {
            if (b.isEmpty(c)) {
                this.iconpicker.find(".iconpicker-item").show();
                return a(false);
            } else {
                var d = [];
                this.iconpicker.find(".iconpicker-item").each(function() {
                    var b = a(this);
                    var e = b.attr("title").toLowerCase();
                    var f = b.attr("data-search-terms") ? b.attr("data-search-terms").toLowerCase() : "";
                    e = e + " " + f;
                    var g = false;
                    try {
                        g = new RegExp("(^|\\W)" + c, "g");
                    } catch (a) {
                        g = false;
                    }
                    if (g !== false && e.match(g)) {
                        d.push(b);
                        b.show();
                    } else {
                        b.hide();
                    }
                });
                return d;
            }
        },
        show: function() {
            if (this.popover.hasClass("in")) {
                return false;
            }
            a.iconpicker.batch(a(".iconpicker-popover.in:not(.inline)").not(this.popover), "hide");
            this._trigger("iconpickerShow", {
                iconpickerValue: this.iconpickerValue
            });
            this.updatePlacement();
            this.popover.addClass("in");
            setTimeout(a.proxy(function() {
                this.popover.css("display", this.isInline() ? "" : "block");
                this._trigger("iconpickerShown", {
                    iconpickerValue: this.iconpickerValue
                });
            }, this), this.options.animation ? 300 : 1);
        },
        hide: function() {
            if (!this.popover.hasClass("in")) {
                return false;
            }
            this._trigger("iconpickerHide", {
                iconpickerValue: this.iconpickerValue
            });
            this.popover.removeClass("in");
            setTimeout(a.proxy(function() {
                this.popover.css("display", "none");
                this.getSearchInput().val("");
                this.filter("");
                this._trigger("iconpickerHidden", {
                    iconpickerValue: this.iconpickerValue
                });
            }, this), this.options.animation ? 300 : 1);
        },
        toggle: function() {
            if (this.popover.is(":visible")) {
                this.hide();
            } else {
                this.show(true);
            }
        },
        update: function(a, b) {
            a = a ? a : this.getSourceValue(this.iconpickerValue);
            this._trigger("iconpickerUpdate", {
                iconpickerValue: this.iconpickerValue
            });
            if (b === true) {
                a = this.setValue(a);
            } else {
                a = this.setSourceValue(a);
                this._updateFormGroupStatus(a !== false);
            }
            if (a !== false) {
                this._updateComponents();
            }
            this._trigger("iconpickerUpdated", {
                iconpickerValue: this.iconpickerValue
            });
            return a;
        },
        destroy: function() {
            this._trigger("iconpickerDestroy", {
                iconpickerValue: this.iconpickerValue
            });
            this.element.removeData("iconpicker").removeData("iconpickerValue").removeClass("iconpicker-element");
            this._unbindElementEvents();
            this._unbindWindowEvents();
            a(this.popover).remove();
            this._trigger("iconpickerDestroyed", {
                iconpickerValue: this.iconpickerValue
            });
        },
        disable: function() {
            if (this.hasInput()) {
                this.input.prop("disabled", true);
                return true;
            }
            return false;
        },
        enable: function() {
            if (this.hasInput()) {
                this.input.prop("disabled", false);
                return true;
            }
            return false;
        },
        isDisabled: function() {
            if (this.hasInput()) {
                return this.input.prop("disabled") === true;
            }
            return false;
        },
        isInline: function() {
            return this.options.placement === "inline" || this.popover.hasClass("inline");
        }
    };
    a.iconpicker = c;
    a.fn.iconpicker = function(b) {
        return this.each(function() {
            var d = a(this);
            if (!d.data("iconpicker")) {
                d.data("iconpicker", new c(this, typeof b === "object" ? b : {}));
            }
        });
    };
    c.defaultOptions = a.extend(c.defaultOptions, {
        icons: [
            {
                "title":"fa-500px",
                "searchTerms":[
                    "fa 500px"
                ]
            },
            {
                "title":"fa-abacus",
                "searchTerms":[
                    "fa abacus"
                ]
            },
            {
                "title":"fa-accessible-icon",
                "searchTerms":[
                    "fa accessible icon"
                ]
            },
            {
                "title":"fa-accusoft",
                "searchTerms":[
                    "fa accusoft"
                ]
            },
            {
                "title":"fa-acorn",
                "searchTerms":[
                    "fa acorn"
                ]
            },
            {
                "title":"fa-acquisitions-incorporated",
                "searchTerms":[
                    "fa acquisitions incorporated"
                ]
            },
            {
                "title":"fa-ad",
                "searchTerms":[
                    "fa ad"
                ]
            },
            {
                "title":"fa-address-book",
                "searchTerms":[
                    "fa address book"
                ]
            },
            {
                "title":"fa-address-card",
                "searchTerms":[
                    "fa address card"
                ]
            },
            {
                "title":"fa-adjust",
                "searchTerms":[
                    "fa adjust"
                ]
            },
            {
                "title":"fa-adn",
                "searchTerms":[
                    "fa adn"
                ]
            },
            {
                "title":"fa-adobe",
                "searchTerms":[
                    "fa adobe"
                ]
            },
            {
                "title":"fa-adversal",
                "searchTerms":[
                    "fa adversal"
                ]
            },
            {
                "title":"fa-affiliatetheme",
                "searchTerms":[
                    "fa affiliatetheme"
                ]
            },
            {
                "title":"fa-air-freshener",
                "searchTerms":[
                    "fa air freshener"
                ]
            },
            {
                "title":"fa-airbnb",
                "searchTerms":[
                    "fa airbnb"
                ]
            },
            {
                "title":"fa-alarm-clock",
                "searchTerms":[
                    "fa alarm clock"
                ]
            },
            {
                "title":"fa-alarm-exclamation",
                "searchTerms":[
                    "fa alarm exclamation"
                ]
            },
            {
                "title":"fa-alarm-plus",
                "searchTerms":[
                    "fa alarm plus"
                ]
            },
            {
                "title":"fa-alarm-snooze",
                "searchTerms":[
                    "fa alarm snooze"
                ]
            },
            {
                "title":"fa-album",
                "searchTerms":[
                    "fa album"
                ]
            },
            {
                "title":"fa-album-collection",
                "searchTerms":[
                    "fa album collection"
                ]
            },
            {
                "title":"fa-algolia",
                "searchTerms":[
                    "fa algolia"
                ]
            },
            {
                "title":"fa-alicorn",
                "searchTerms":[
                    "fa alicorn"
                ]
            },
            {
                "title":"fa-align-center",
                "searchTerms":[
                    "fa align center"
                ]
            },
            {
                "title":"fa-align-justify",
                "searchTerms":[
                    "fa align justify"
                ]
            },
            {
                "title":"fa-align-left",
                "searchTerms":[
                    "fa align left"
                ]
            },
            {
                "title":"fa-align-right",
                "searchTerms":[
                    "fa align right"
                ]
            },
            {
                "title":"fa-align-slash",
                "searchTerms":[
                    "fa align slash"
                ]
            },
            {
                "title":"fa-alipay",
                "searchTerms":[
                    "fa alipay"
                ]
            },
            {
                "title":"fa-allergies",
                "searchTerms":[
                    "fa allergies"
                ]
            },
            {
                "title":"fa-amazon",
                "searchTerms":[
                    "fa amazon"
                ]
            },
            {
                "title":"fa-amazon-pay",
                "searchTerms":[
                    "fa amazon pay"
                ]
            },
            {
                "title":"fa-ambulance",
                "searchTerms":[
                    "fa ambulance"
                ]
            },
            {
                "title":"fa-american-sign-language-interpreting",
                "searchTerms":[
                    "fa american sign language interpreting"
                ]
            },
            {
                "title":"fa-amilia",
                "searchTerms":[
                    "fa amilia"
                ]
            },
            {
                "title":"fa-amp-guitar",
                "searchTerms":[
                    "fa amp guitar"
                ]
            },
            {
                "title":"fa-analytics",
                "searchTerms":[
                    "fa analytics"
                ]
            },
            {
                "title":"fa-anchor",
                "searchTerms":[
                    "fa anchor"
                ]
            },
            {
                "title":"fa-android",
                "searchTerms":[
                    "fa android"
                ]
            },
            {
                "title":"fa-angel",
                "searchTerms":[
                    "fa angel"
                ]
            },
            {
                "title":"fa-angellist",
                "searchTerms":[
                    "fa angellist"
                ]
            },
            {
                "title":"fa-angle-double-down",
                "searchTerms":[
                    "fa angle double down"
                ]
            },
            {
                "title":"fa-angle-double-left",
                "searchTerms":[
                    "fa angle double left"
                ]
            },
            {
                "title":"fa-angle-double-right",
                "searchTerms":[
                    "fa angle double right"
                ]
            },
            {
                "title":"fa-angle-double-up",
                "searchTerms":[
                    "fa angle double up"
                ]
            },
            {
                "title":"fa-angle-down",
                "searchTerms":[
                    "fa angle down"
                ]
            },
            {
                "title":"fa-angle-left",
                "searchTerms":[
                    "fa angle left"
                ]
            },
            {
                "title":"fa-angle-right",
                "searchTerms":[
                    "fa angle right"
                ]
            },
            {
                "title":"fa-angle-up",
                "searchTerms":[
                    "fa angle up"
                ]
            },
            {
                "title":"fa-angry",
                "searchTerms":[
                    "fa angry"
                ]
            },
            {
                "title":"fa-angrycreative",
                "searchTerms":[
                    "fa angrycreative"
                ]
            },
            {
                "title":"fa-angular",
                "searchTerms":[
                    "fa angular"
                ]
            },
            {
                "title":"fa-ankh",
                "searchTerms":[
                    "fa ankh"
                ]
            },
            {
                "title":"fa-app-store",
                "searchTerms":[
                    "fa app store"
                ]
            },
            {
                "title":"fa-app-store-ios",
                "searchTerms":[
                    "fa app store ios"
                ]
            },
            {
                "title":"fa-apper",
                "searchTerms":[
                    "fa apper"
                ]
            },
            {
                "title":"fa-apple",
                "searchTerms":[
                    "fa apple"
                ]
            },
            {
                "title":"fa-apple-alt",
                "searchTerms":[
                    "fa apple alt"
                ]
            },
            {
                "title":"fa-apple-crate",
                "searchTerms":[
                    "fa apple crate"
                ]
            },
            {
                "title":"fa-apple-pay",
                "searchTerms":[
                    "fa apple pay"
                ]
            },
            {
                "title":"fa-archive",
                "searchTerms":[
                    "fa archive"
                ]
            },
            {
                "title":"fa-archway",
                "searchTerms":[
                    "fa archway"
                ]
            },
            {
                "title":"fa-arrow-alt-circle-down",
                "searchTerms":[
                    "fa arrow alt circle down"
                ]
            },
            {
                "title":"fa-arrow-alt-circle-left",
                "searchTerms":[
                    "fa arrow alt circle left"
                ]
            },
            {
                "title":"fa-arrow-alt-circle-right",
                "searchTerms":[
                    "fa arrow alt circle right"
                ]
            },
            {
                "title":"fa-arrow-alt-circle-up",
                "searchTerms":[
                    "fa arrow alt circle up"
                ]
            },
            {
                "title":"fa-arrow-alt-down",
                "searchTerms":[
                    "fa arrow alt down"
                ]
            },
            {
                "title":"fa-arrow-alt-from-bottom",
                "searchTerms":[
                    "fa arrow alt from bottom"
                ]
            },
            {
                "title":"fa-arrow-alt-from-left",
                "searchTerms":[
                    "fa arrow alt from left"
                ]
            },
            {
                "title":"fa-arrow-alt-from-right",
                "searchTerms":[
                    "fa arrow alt from right"
                ]
            },
            {
                "title":"fa-arrow-alt-from-top",
                "searchTerms":[
                    "fa arrow alt from top"
                ]
            },
            {
                "title":"fa-arrow-alt-left",
                "searchTerms":[
                    "fa arrow alt left"
                ]
            },
            {
                "title":"fa-arrow-alt-right",
                "searchTerms":[
                    "fa arrow alt right"
                ]
            },
            {
                "title":"fa-arrow-alt-square-down",
                "searchTerms":[
                    "fa arrow alt square down"
                ]
            },
            {
                "title":"fa-arrow-alt-square-left",
                "searchTerms":[
                    "fa arrow alt square left"
                ]
            },
            {
                "title":"fa-arrow-alt-square-right",
                "searchTerms":[
                    "fa arrow alt square right"
                ]
            },
            {
                "title":"fa-arrow-alt-square-up",
                "searchTerms":[
                    "fa arrow alt square up"
                ]
            },
            {
                "title":"fa-arrow-alt-to-bottom",
                "searchTerms":[
                    "fa arrow alt to bottom"
                ]
            },
            {
                "title":"fa-arrow-alt-to-left",
                "searchTerms":[
                    "fa arrow alt to left"
                ]
            },
            {
                "title":"fa-arrow-alt-to-right",
                "searchTerms":[
                    "fa arrow alt to right"
                ]
            },
            {
                "title":"fa-arrow-alt-to-top",
                "searchTerms":[
                    "fa arrow alt to top"
                ]
            },
            {
                "title":"fa-arrow-alt-up",
                "searchTerms":[
                    "fa arrow alt up"
                ]
            },
            {
                "title":"fa-arrow-circle-down",
                "searchTerms":[
                    "fa arrow circle down"
                ]
            },
            {
                "title":"fa-arrow-circle-left",
                "searchTerms":[
                    "fa arrow circle left"
                ]
            },
            {
                "title":"fa-arrow-circle-right",
                "searchTerms":[
                    "fa arrow circle right"
                ]
            },
            {
                "title":"fa-arrow-circle-up",
                "searchTerms":[
                    "fa arrow circle up"
                ]
            },
            {
                "title":"fa-arrow-down",
                "searchTerms":[
                    "fa arrow down"
                ]
            },
            {
                "title":"fa-arrow-from-bottom",
                "searchTerms":[
                    "fa arrow from bottom"
                ]
            },
            {
                "title":"fa-arrow-from-left",
                "searchTerms":[
                    "fa arrow from left"
                ]
            },
            {
                "title":"fa-arrow-from-right",
                "searchTerms":[
                    "fa arrow from right"
                ]
            },
            {
                "title":"fa-arrow-from-top",
                "searchTerms":[
                    "fa arrow from top"
                ]
            },
            {
                "title":"fa-arrow-left",
                "searchTerms":[
                    "fa arrow left"
                ]
            },
            {
                "title":"fa-arrow-right",
                "searchTerms":[
                    "fa arrow right"
                ]
            },
            {
                "title":"fa-arrow-square-down",
                "searchTerms":[
                    "fa arrow square down"
                ]
            },
            {
                "title":"fa-arrow-square-left",
                "searchTerms":[
                    "fa arrow square left"
                ]
            },
            {
                "title":"fa-arrow-square-right",
                "searchTerms":[
                    "fa arrow square right"
                ]
            },
            {
                "title":"fa-arrow-square-up",
                "searchTerms":[
                    "fa arrow square up"
                ]
            },
            {
                "title":"fa-arrow-to-bottom",
                "searchTerms":[
                    "fa arrow to bottom"
                ]
            },
            {
                "title":"fa-arrow-to-left",
                "searchTerms":[
                    "fa arrow to left"
                ]
            },
            {
                "title":"fa-arrow-to-right",
                "searchTerms":[
                    "fa arrow to right"
                ]
            },
            {
                "title":"fa-arrow-to-top",
                "searchTerms":[
                    "fa arrow to top"
                ]
            },
            {
                "title":"fa-arrow-up",
                "searchTerms":[
                    "fa arrow up"
                ]
            },
            {
                "title":"fa-arrows",
                "searchTerms":[
                    "fa arrows"
                ]
            },
            {
                "title":"fa-arrows-alt",
                "searchTerms":[
                    "fa arrows alt"
                ]
            },
            {
                "title":"fa-arrows-alt-h",
                "searchTerms":[
                    "fa arrows alt h"
                ]
            },
            {
                "title":"fa-arrows-alt-v",
                "searchTerms":[
                    "fa arrows alt v"
                ]
            },
            {
                "title":"fa-arrows-h",
                "searchTerms":[
                    "fa arrows h"
                ]
            },
            {
                "title":"fa-arrows-v",
                "searchTerms":[
                    "fa arrows v"
                ]
            },
            {
                "title":"fa-artstation",
                "searchTerms":[
                    "fa artstation"
                ]
            },
            {
                "title":"fa-assistive-listening-systems",
                "searchTerms":[
                    "fa assistive listening systems"
                ]
            },
            {
                "title":"fa-asterisk",
                "searchTerms":[
                    "fa asterisk"
                ]
            },
            {
                "title":"fa-asymmetrik",
                "searchTerms":[
                    "fa asymmetrik"
                ]
            },
            {
                "title":"fa-at",
                "searchTerms":[
                    "fa at"
                ]
            },
            {
                "title":"fa-atlas",
                "searchTerms":[
                    "fa atlas"
                ]
            },
            {
                "title":"fa-atlassian",
                "searchTerms":[
                    "fa atlassian"
                ]
            },
            {
                "title":"fa-atom",
                "searchTerms":[
                    "fa atom"
                ]
            },
            {
                "title":"fa-atom-alt",
                "searchTerms":[
                    "fa atom alt"
                ]
            },
            {
                "title":"fa-audible",
                "searchTerms":[
                    "fa audible"
                ]
            },
            {
                "title":"fa-audio-description",
                "searchTerms":[
                    "fa audio description"
                ]
            },
            {
                "title":"fa-autoprefixer",
                "searchTerms":[
                    "fa autoprefixer"
                ]
            },
            {
                "title":"fa-avianex",
                "searchTerms":[
                    "fa avianex"
                ]
            },
            {
                "title":"fa-aviato",
                "searchTerms":[
                    "fa aviato"
                ]
            },
            {
                "title":"fa-award",
                "searchTerms":[
                    "fa award"
                ]
            },
            {
                "title":"fa-aws",
                "searchTerms":[
                    "fa aws"
                ]
            },
            {
                "title":"fa-axe",
                "searchTerms":[
                    "fa axe"
                ]
            },
            {
                "title":"fa-axe-battle",
                "searchTerms":[
                    "fa axe battle"
                ]
            },
            {
                "title":"fa-baby",
                "searchTerms":[
                    "fa baby"
                ]
            },
            {
                "title":"fa-baby-carriage",
                "searchTerms":[
                    "fa baby carriage"
                ]
            },
            {
                "title":"fa-backpack",
                "searchTerms":[
                    "fa backpack"
                ]
            },
            {
                "title":"fa-backspace",
                "searchTerms":[
                    "fa backspace"
                ]
            },
            {
                "title":"fa-backward",
                "searchTerms":[
                    "fa backward"
                ]
            },
            {
                "title":"fa-bacon",
                "searchTerms":[
                    "fa bacon"
                ]
            },
            {
                "title":"fa-badge",
                "searchTerms":[
                    "fa badge"
                ]
            },
            {
                "title":"fa-badge-check",
                "searchTerms":[
                    "fa badge check"
                ]
            },
            {
                "title":"fa-badge-dollar",
                "searchTerms":[
                    "fa badge dollar"
                ]
            },
            {
                "title":"fa-badge-percent",
                "searchTerms":[
                    "fa badge percent"
                ]
            },
            {
                "title":"fa-badge-sheriff",
                "searchTerms":[
                    "fa badge sheriff"
                ]
            },
            {
                "title":"fa-badger-honey",
                "searchTerms":[
                    "fa badger honey"
                ]
            },
            {
                "title":"fa-bags-shopping",
                "searchTerms":[
                    "fa bags shopping"
                ]
            },
            {
                "title":"fa-balance-scale",
                "searchTerms":[
                    "fa balance scale"
                ]
            },
            {
                "title":"fa-balance-scale-left",
                "searchTerms":[
                    "fa balance scale left"
                ]
            },
            {
                "title":"fa-balance-scale-right",
                "searchTerms":[
                    "fa balance scale right"
                ]
            },
            {
                "title":"fa-ball-pile",
                "searchTerms":[
                    "fa ball pile"
                ]
            },
            {
                "title":"fa-ballot",
                "searchTerms":[
                    "fa ballot"
                ]
            },
            {
                "title":"fa-ballot-check",
                "searchTerms":[
                    "fa ballot check"
                ]
            },
            {
                "title":"fa-ban",
                "searchTerms":[
                    "fa ban"
                ]
            },
            {
                "title":"fa-band-aid",
                "searchTerms":[
                    "fa band aid"
                ]
            },
            {
                "title":"fa-bandcamp",
                "searchTerms":[
                    "fa bandcamp"
                ]
            },
            {
                "title":"fa-banjo",
                "searchTerms":[
                    "fa banjo"
                ]
            },
            {
                "title":"fa-barcode",
                "searchTerms":[
                    "fa barcode"
                ]
            },
            {
                "title":"fa-barcode-alt",
                "searchTerms":[
                    "fa barcode alt"
                ]
            },
            {
                "title":"fa-barcode-read",
                "searchTerms":[
                    "fa barcode read"
                ]
            },
            {
                "title":"fa-barcode-scan",
                "searchTerms":[
                    "fa barcode scan"
                ]
            },
            {
                "title":"fa-bars",
                "searchTerms":[
                    "fa bars"
                ]
            },
            {
                "title":"fa-baseball",
                "searchTerms":[
                    "fa baseball"
                ]
            },
            {
                "title":"fa-baseball-ball",
                "searchTerms":[
                    "fa baseball ball"
                ]
            },
            {
                "title":"fa-basketball-ball",
                "searchTerms":[
                    "fa basketball ball"
                ]
            },
            {
                "title":"fa-basketball-hoop",
                "searchTerms":[
                    "fa basketball hoop"
                ]
            },
            {
                "title":"fa-bat",
                "searchTerms":[
                    "fa bat"
                ]
            },
            {
                "title":"fa-bath",
                "searchTerms":[
                    "fa bath"
                ]
            },
            {
                "title":"fa-battery-bolt",
                "searchTerms":[
                    "fa battery bolt"
                ]
            },
            {
                "title":"fa-battery-empty",
                "searchTerms":[
                    "fa battery empty"
                ]
            },
            {
                "title":"fa-battery-full",
                "searchTerms":[
                    "fa battery full"
                ]
            },
            {
                "title":"fa-battery-half",
                "searchTerms":[
                    "fa battery half"
                ]
            },
            {
                "title":"fa-battery-quarter",
                "searchTerms":[
                    "fa battery quarter"
                ]
            },
            {
                "title":"fa-battery-slash",
                "searchTerms":[
                    "fa battery slash"
                ]
            },
            {
                "title":"fa-battery-three-quarters",
                "searchTerms":[
                    "fa battery three quarters"
                ]
            },
            {
                "title":"fa-battle-net",
                "searchTerms":[
                    "fa battle net"
                ]
            },
            {
                "title":"fa-bed",
                "searchTerms":[
                    "fa bed"
                ]
            },
            {
                "title":"fa-beer",
                "searchTerms":[
                    "fa beer"
                ]
            },
            {
                "title":"fa-behance",
                "searchTerms":[
                    "fa behance"
                ]
            },
            {
                "title":"fa-behance-square",
                "searchTerms":[
                    "fa behance square"
                ]
            },
            {
                "title":"fa-bell",
                "searchTerms":[
                    "fa bell"
                ]
            },
            {
                "title":"fa-bell-exclamation",
                "searchTerms":[
                    "fa bell exclamation"
                ]
            },
            {
                "title":"fa-bell-plus",
                "searchTerms":[
                    "fa bell plus"
                ]
            },
            {
                "title":"fa-bell-school",
                "searchTerms":[
                    "fa bell school"
                ]
            },
            {
                "title":"fa-bell-school-slash",
                "searchTerms":[
                    "fa bell school slash"
                ]
            },
            {
                "title":"fa-bell-slash",
                "searchTerms":[
                    "fa bell slash"
                ]
            },
            {
                "title":"fa-bells",
                "searchTerms":[
                    "fa bells"
                ]
            },
            {
                "title":"fa-betamax",
                "searchTerms":[
                    "fa betamax"
                ]
            },
            {
                "title":"fa-bezier-curve",
                "searchTerms":[
                    "fa bezier curve"
                ]
            },
            {
                "title":"fa-bible",
                "searchTerms":[
                    "fa bible"
                ]
            },
            {
                "title":"fa-bicycle",
                "searchTerms":[
                    "fa bicycle"
                ]
            },
            {
                "title":"fa-biking",
                "searchTerms":[
                    "fa biking"
                ]
            },
            {
                "title":"fa-biking-mountain",
                "searchTerms":[
                    "fa biking mountain"
                ]
            },
            {
                "title":"fa-bimobject",
                "searchTerms":[
                    "fa bimobject"
                ]
            },
            {
                "title":"fa-binoculars",
                "searchTerms":[
                    "fa binoculars"
                ]
            },
            {
                "title":"fa-biohazard",
                "searchTerms":[
                    "fa biohazard"
                ]
            },
            {
                "title":"fa-birthday-cake",
                "searchTerms":[
                    "fa birthday cake"
                ]
            },
            {
                "title":"fa-bitbucket",
                "searchTerms":[
                    "fa bitbucket"
                ]
            },
            {
                "title":"fa-bitcoin",
                "searchTerms":[
                    "fa bitcoin"
                ]
            },
            {
                "title":"fa-bity",
                "searchTerms":[
                    "fa bity"
                ]
            },
            {
                "title":"fa-black-tie",
                "searchTerms":[
                    "fa black tie"
                ]
            },
            {
                "title":"fa-blackberry",
                "searchTerms":[
                    "fa blackberry"
                ]
            },
            {
                "title":"fa-blanket",
                "searchTerms":[
                    "fa blanket"
                ]
            },
            {
                "title":"fa-blender",
                "searchTerms":[
                    "fa blender"
                ]
            },
            {
                "title":"fa-blender-phone",
                "searchTerms":[
                    "fa blender phone"
                ]
            },
            {
                "title":"fa-blind",
                "searchTerms":[
                    "fa blind"
                ]
            },
            {
                "title":"fa-blog",
                "searchTerms":[
                    "fa blog"
                ]
            },
            {
                "title":"fa-blogger",
                "searchTerms":[
                    "fa blogger"
                ]
            },
            {
                "title":"fa-blogger-b",
                "searchTerms":[
                    "fa blogger b"
                ]
            },
            {
                "title":"fa-bluetooth",
                "searchTerms":[
                    "fa bluetooth"
                ]
            },
            {
                "title":"fa-bluetooth-b",
                "searchTerms":[
                    "fa bluetooth b"
                ]
            },
            {
                "title":"fa-bold",
                "searchTerms":[
                    "fa bold"
                ]
            },
            {
                "title":"fa-bolt",
                "searchTerms":[
                    "fa bolt"
                ]
            },
            {
                "title":"fa-bomb",
                "searchTerms":[
                    "fa bomb"
                ]
            },
            {
                "title":"fa-bone",
                "searchTerms":[
                    "fa bone"
                ]
            },
            {
                "title":"fa-bone-break",
                "searchTerms":[
                    "fa bone break"
                ]
            },
            {
                "title":"fa-bong",
                "searchTerms":[
                    "fa bong"
                ]
            },
            {
                "title":"fa-book",
                "searchTerms":[
                    "fa book"
                ]
            },
            {
                "title":"fa-book-alt",
                "searchTerms":[
                    "fa book alt"
                ]
            },
            {
                "title":"fa-book-dead",
                "searchTerms":[
                    "fa book dead"
                ]
            },
            {
                "title":"fa-book-heart",
                "searchTerms":[
                    "fa book heart"
                ]
            },
            {
                "title":"fa-book-medical",
                "searchTerms":[
                    "fa book medical"
                ]
            },
            {
                "title":"fa-book-open",
                "searchTerms":[
                    "fa book open"
                ]
            },
            {
                "title":"fa-book-reader",
                "searchTerms":[
                    "fa book reader"
                ]
            },
            {
                "title":"fa-book-spells",
                "searchTerms":[
                    "fa book spells"
                ]
            },
            {
                "title":"fa-book-user",
                "searchTerms":[
                    "fa book user"
                ]
            },
            {
                "title":"fa-bookmark",
                "searchTerms":[
                    "fa bookmark"
                ]
            },
            {
                "title":"fa-books",
                "searchTerms":[
                    "fa books"
                ]
            },
            {
                "title":"fa-books-medical",
                "searchTerms":[
                    "fa books medical"
                ]
            },
            {
                "title":"fa-boombox",
                "searchTerms":[
                    "fa boombox"
                ]
            },
            {
                "title":"fa-boot",
                "searchTerms":[
                    "fa boot"
                ]
            },
            {
                "title":"fa-booth-curtain",
                "searchTerms":[
                    "fa booth curtain"
                ]
            },
            {
                "title":"fa-bootstrap",
                "searchTerms":[
                    "fa bootstrap"
                ]
            },
            {
                "title":"fa-border-all",
                "searchTerms":[
                    "fa border all"
                ]
            },
            {
                "title":"fa-border-bottom",
                "searchTerms":[
                    "fa border bottom"
                ]
            },
            {
                "title":"fa-border-center-h",
                "searchTerms":[
                    "fa border center h"
                ]
            },
            {
                "title":"fa-border-center-v",
                "searchTerms":[
                    "fa border center v"
                ]
            },
            {
                "title":"fa-border-inner",
                "searchTerms":[
                    "fa border inner"
                ]
            },
            {
                "title":"fa-border-left",
                "searchTerms":[
                    "fa border left"
                ]
            },
            {
                "title":"fa-border-none",
                "searchTerms":[
                    "fa border none"
                ]
            },
            {
                "title":"fa-border-outer",
                "searchTerms":[
                    "fa border outer"
                ]
            },
            {
                "title":"fa-border-right",
                "searchTerms":[
                    "fa border right"
                ]
            },
            {
                "title":"fa-border-style",
                "searchTerms":[
                    "fa border style"
                ]
            },
            {
                "title":"fa-border-style-alt",
                "searchTerms":[
                    "fa border style alt"
                ]
            },
            {
                "title":"fa-border-top",
                "searchTerms":[
                    "fa border top"
                ]
            },
            {
                "title":"fa-bow-arrow",
                "searchTerms":[
                    "fa bow arrow"
                ]
            },
            {
                "title":"fa-bowling-ball",
                "searchTerms":[
                    "fa bowling ball"
                ]
            },
            {
                "title":"fa-bowling-pins",
                "searchTerms":[
                    "fa bowling pins"
                ]
            },
            {
                "title":"fa-box",
                "searchTerms":[
                    "fa box"
                ]
            },
            {
                "title":"fa-box-alt",
                "searchTerms":[
                    "fa box alt"
                ]
            },
            {
                "title":"fa-box-ballot",
                "searchTerms":[
                    "fa box ballot"
                ]
            },
            {
                "title":"fa-box-check",
                "searchTerms":[
                    "fa box check"
                ]
            },
            {
                "title":"fa-box-fragile",
                "searchTerms":[
                    "fa box fragile"
                ]
            },
            {
                "title":"fa-box-full",
                "searchTerms":[
                    "fa box full"
                ]
            },
            {
                "title":"fa-box-heart",
                "searchTerms":[
                    "fa box heart"
                ]
            },
            {
                "title":"fa-box-open",
                "searchTerms":[
                    "fa box open"
                ]
            },
            {
                "title":"fa-box-up",
                "searchTerms":[
                    "fa box up"
                ]
            },
            {
                "title":"fa-box-usd",
                "searchTerms":[
                    "fa box usd"
                ]
            },
            {
                "title":"fa-boxes",
                "searchTerms":[
                    "fa boxes"
                ]
            },
            {
                "title":"fa-boxes-alt",
                "searchTerms":[
                    "fa boxes alt"
                ]
            },
            {
                "title":"fa-boxing-glove",
                "searchTerms":[
                    "fa boxing glove"
                ]
            },
            {
                "title":"fa-brackets",
                "searchTerms":[
                    "fa brackets"
                ]
            },
            {
                "title":"fa-brackets-curly",
                "searchTerms":[
                    "fa brackets curly"
                ]
            },
            {
                "title":"fa-braille",
                "searchTerms":[
                    "fa braille"
                ]
            },
            {
                "title":"fa-brain",
                "searchTerms":[
                    "fa brain"
                ]
            },
            {
                "title":"fa-bread-loaf",
                "searchTerms":[
                    "fa bread loaf"
                ]
            },
            {
                "title":"fa-bread-slice",
                "searchTerms":[
                    "fa bread slice"
                ]
            },
            {
                "title":"fa-briefcase",
                "searchTerms":[
                    "fa briefcase"
                ]
            },
            {
                "title":"fa-briefcase-medical",
                "searchTerms":[
                    "fa briefcase medical"
                ]
            },
            {
                "title":"fa-bring-forward",
                "searchTerms":[
                    "fa bring forward"
                ]
            },
            {
                "title":"fa-bring-front",
                "searchTerms":[
                    "fa bring front"
                ]
            },
            {
                "title":"fa-broadcast-tower",
                "searchTerms":[
                    "fa broadcast tower"
                ]
            },
            {
                "title":"fa-broom",
                "searchTerms":[
                    "fa broom"
                ]
            },
            {
                "title":"fa-browser",
                "searchTerms":[
                    "fa browser"
                ]
            },
            {
                "title":"fa-brush",
                "searchTerms":[
                    "fa brush"
                ]
            },
            {
                "title":"fa-btc",
                "searchTerms":[
                    "fa btc"
                ]
            },
            {
                "title":"fa-buffer",
                "searchTerms":[
                    "fa buffer"
                ]
            },
            {
                "title":"fa-bug",
                "searchTerms":[
                    "fa bug"
                ]
            },
            {
                "title":"fa-building",
                "searchTerms":[
                    "fa building"
                ]
            },
            {
                "title":"fa-bullhorn",
                "searchTerms":[
                    "fa bullhorn"
                ]
            },
            {
                "title":"fa-bullseye",
                "searchTerms":[
                    "fa bullseye"
                ]
            },
            {
                "title":"fa-bullseye-arrow",
                "searchTerms":[
                    "fa bullseye arrow"
                ]
            },
            {
                "title":"fa-bullseye-pointer",
                "searchTerms":[
                    "fa bullseye pointer"
                ]
            },
            {
                "title":"fa-burger-soda",
                "searchTerms":[
                    "fa burger soda"
                ]
            },
            {
                "title":"fa-burn",
                "searchTerms":[
                    "fa burn"
                ]
            },
            {
                "title":"fa-buromobelexperte",
                "searchTerms":[
                    "fa buromobelexperte"
                ]
            },
            {
                "title":"fa-burrito",
                "searchTerms":[
                    "fa burrito"
                ]
            },
            {
                "title":"fa-bus",
                "searchTerms":[
                    "fa bus"
                ]
            },
            {
                "title":"fa-bus-alt",
                "searchTerms":[
                    "fa bus alt"
                ]
            },
            {
                "title":"fa-bus-school",
                "searchTerms":[
                    "fa bus school"
                ]
            },
            {
                "title":"fa-business-time",
                "searchTerms":[
                    "fa business time"
                ]
            },
            {
                "title":"fa-buy-n-large",
                "searchTerms":[
                    "fa buy n large"
                ]
            },
            {
                "title":"fa-buysellads",
                "searchTerms":[
                    "fa buysellads"
                ]
            },
            {
                "title":"fa-cabinet-filing",
                "searchTerms":[
                    "fa cabinet filing"
                ]
            },
            {
                "title":"fa-cactus",
                "searchTerms":[
                    "fa cactus"
                ]
            },
            {
                "title":"fa-calculator",
                "searchTerms":[
                    "fa calculator"
                ]
            },
            {
                "title":"fa-calculator-alt",
                "searchTerms":[
                    "fa calculator alt"
                ]
            },
            {
                "title":"fa-calendar",
                "searchTerms":[
                    "fa calendar"
                ]
            },
            {
                "title":"fa-calendar-alt",
                "searchTerms":[
                    "fa calendar alt"
                ]
            },
            {
                "title":"fa-calendar-check",
                "searchTerms":[
                    "fa calendar check"
                ]
            },
            {
                "title":"fa-calendar-day",
                "searchTerms":[
                    "fa calendar day"
                ]
            },
            {
                "title":"fa-calendar-edit",
                "searchTerms":[
                    "fa calendar edit"
                ]
            },
            {
                "title":"fa-calendar-exclamation",
                "searchTerms":[
                    "fa calendar exclamation"
                ]
            },
            {
                "title":"fa-calendar-minus",
                "searchTerms":[
                    "fa calendar minus"
                ]
            },
            {
                "title":"fa-calendar-plus",
                "searchTerms":[
                    "fa calendar plus"
                ]
            },
            {
                "title":"fa-calendar-star",
                "searchTerms":[
                    "fa calendar star"
                ]
            },
            {
                "title":"fa-calendar-times",
                "searchTerms":[
                    "fa calendar times"
                ]
            },
            {
                "title":"fa-calendar-week",
                "searchTerms":[
                    "fa calendar week"
                ]
            },
            {
                "title":"fa-camcorder",
                "searchTerms":[
                    "fa camcorder"
                ]
            },
            {
                "title":"fa-camera",
                "searchTerms":[
                    "fa camera"
                ]
            },
            {
                "title":"fa-camera-alt",
                "searchTerms":[
                    "fa camera alt"
                ]
            },
            {
                "title":"fa-camera-movie",
                "searchTerms":[
                    "fa camera movie"
                ]
            },
            {
                "title":"fa-camera-polaroid",
                "searchTerms":[
                    "fa camera polaroid"
                ]
            },
            {
                "title":"fa-camera-retro",
                "searchTerms":[
                    "fa camera retro"
                ]
            },
            {
                "title":"fa-campfire",
                "searchTerms":[
                    "fa campfire"
                ]
            },
            {
                "title":"fa-campground",
                "searchTerms":[
                    "fa campground"
                ]
            },
            {
                "title":"fa-canadian-maple-leaf",
                "searchTerms":[
                    "fa canadian maple leaf"
                ]
            },
            {
                "title":"fa-candle-holder",
                "searchTerms":[
                    "fa candle holder"
                ]
            },
            {
                "title":"fa-candy-cane",
                "searchTerms":[
                    "fa candy cane"
                ]
            },
            {
                "title":"fa-candy-corn",
                "searchTerms":[
                    "fa candy corn"
                ]
            },
            {
                "title":"fa-cannabis",
                "searchTerms":[
                    "fa cannabis"
                ]
            },
            {
                "title":"fa-capsules",
                "searchTerms":[
                    "fa capsules"
                ]
            },
            {
                "title":"fa-car",
                "searchTerms":[
                    "fa car"
                ]
            },
            {
                "title":"fa-car-alt",
                "searchTerms":[
                    "fa car alt"
                ]
            },
            {
                "title":"fa-car-battery",
                "searchTerms":[
                    "fa car battery"
                ]
            },
            {
                "title":"fa-car-building",
                "searchTerms":[
                    "fa car building"
                ]
            },
            {
                "title":"fa-car-bump",
                "searchTerms":[
                    "fa car bump"
                ]
            },
            {
                "title":"fa-car-bus",
                "searchTerms":[
                    "fa car bus"
                ]
            },
            {
                "title":"fa-car-crash",
                "searchTerms":[
                    "fa car crash"
                ]
            },
            {
                "title":"fa-car-garage",
                "searchTerms":[
                    "fa car garage"
                ]
            },
            {
                "title":"fa-car-mechanic",
                "searchTerms":[
                    "fa car mechanic"
                ]
            },
            {
                "title":"fa-car-side",
                "searchTerms":[
                    "fa car side"
                ]
            },
            {
                "title":"fa-car-tilt",
                "searchTerms":[
                    "fa car tilt"
                ]
            },
            {
                "title":"fa-car-wash",
                "searchTerms":[
                    "fa car wash"
                ]
            },
            {
                "title":"fa-caret-circle-down",
                "searchTerms":[
                    "fa caret circle down"
                ]
            },
            {
                "title":"fa-caret-circle-left",
                "searchTerms":[
                    "fa caret circle left"
                ]
            },
            {
                "title":"fa-caret-circle-right",
                "searchTerms":[
                    "fa caret circle right"
                ]
            },
            {
                "title":"fa-caret-circle-up",
                "searchTerms":[
                    "fa caret circle up"
                ]
            },
            {
                "title":"fa-caret-down",
                "searchTerms":[
                    "fa caret down"
                ]
            },
            {
                "title":"fa-caret-left",
                "searchTerms":[
                    "fa caret left"
                ]
            },
            {
                "title":"fa-caret-right",
                "searchTerms":[
                    "fa caret right"
                ]
            },
            {
                "title":"fa-caret-square-down",
                "searchTerms":[
                    "fa caret square down"
                ]
            },
            {
                "title":"fa-caret-square-left",
                "searchTerms":[
                    "fa caret square left"
                ]
            },
            {
                "title":"fa-caret-square-right",
                "searchTerms":[
                    "fa caret square right"
                ]
            },
            {
                "title":"fa-caret-square-up",
                "searchTerms":[
                    "fa caret square up"
                ]
            },
            {
                "title":"fa-caret-up",
                "searchTerms":[
                    "fa caret up"
                ]
            },
            {
                "title":"fa-carrot",
                "searchTerms":[
                    "fa carrot"
                ]
            },
            {
                "title":"fa-cars",
                "searchTerms":[
                    "fa cars"
                ]
            },
            {
                "title":"fa-cart-arrow-down",
                "searchTerms":[
                    "fa cart arrow down"
                ]
            },
            {
                "title":"fa-cart-plus",
                "searchTerms":[
                    "fa cart plus"
                ]
            },
            {
                "title":"fa-cash-register",
                "searchTerms":[
                    "fa cash register"
                ]
            },
            {
                "title":"fa-cassette-tape",
                "searchTerms":[
                    "fa cassette tape"
                ]
            },
            {
                "title":"fa-cat",
                "searchTerms":[
                    "fa cat"
                ]
            },
            {
                "title":"fa-cauldron",
                "searchTerms":[
                    "fa cauldron"
                ]
            },
            {
                "title":"fa-cc-amazon-pay",
                "searchTerms":[
                    "fa cc amazon pay"
                ]
            },
            {
                "title":"fa-cc-amex",
                "searchTerms":[
                    "fa cc amex"
                ]
            },
            {
                "title":"fa-cc-apple-pay",
                "searchTerms":[
                    "fa cc apple pay"
                ]
            },
            {
                "title":"fa-cc-diners-club",
                "searchTerms":[
                    "fa cc diners club"
                ]
            },
            {
                "title":"fa-cc-discover",
                "searchTerms":[
                    "fa cc discover"
                ]
            },
            {
                "title":"fa-cc-jcb",
                "searchTerms":[
                    "fa cc jcb"
                ]
            },
            {
                "title":"fa-cc-mastercard",
                "searchTerms":[
                    "fa cc mastercard"
                ]
            },
            {
                "title":"fa-cc-paypal",
                "searchTerms":[
                    "fa cc paypal"
                ]
            },
            {
                "title":"fa-cc-stripe",
                "searchTerms":[
                    "fa cc stripe"
                ]
            },
            {
                "title":"fa-cc-visa",
                "searchTerms":[
                    "fa cc visa"
                ]
            },
            {
                "title":"fa-cctv",
                "searchTerms":[
                    "fa cctv"
                ]
            },
            {
                "title":"fa-centercode",
                "searchTerms":[
                    "fa centercode"
                ]
            },
            {
                "title":"fa-centos",
                "searchTerms":[
                    "fa centos"
                ]
            },
            {
                "title":"fa-certificate",
                "searchTerms":[
                    "fa certificate"
                ]
            },
            {
                "title":"fa-chair",
                "searchTerms":[
                    "fa chair"
                ]
            },
            {
                "title":"fa-chair-office",
                "searchTerms":[
                    "fa chair office"
                ]
            },
            {
                "title":"fa-chalkboard",
                "searchTerms":[
                    "fa chalkboard"
                ]
            },
            {
                "title":"fa-chalkboard-teacher",
                "searchTerms":[
                    "fa chalkboard teacher"
                ]
            },
            {
                "title":"fa-charging-station",
                "searchTerms":[
                    "fa charging station"
                ]
            },
            {
                "title":"fa-chart-area",
                "searchTerms":[
                    "fa chart area"
                ]
            },
            {
                "title":"fa-chart-bar",
                "searchTerms":[
                    "fa chart bar"
                ]
            },
            {
                "title":"fa-chart-line",
                "searchTerms":[
                    "fa chart line"
                ]
            },
            {
                "title":"fa-chart-line-down",
                "searchTerms":[
                    "fa chart line down"
                ]
            },
            {
                "title":"fa-chart-network",
                "searchTerms":[
                    "fa chart network"
                ]
            },
            {
                "title":"fa-chart-pie",
                "searchTerms":[
                    "fa chart pie"
                ]
            },
            {
                "title":"fa-chart-pie-alt",
                "searchTerms":[
                    "fa chart pie alt"
                ]
            },
            {
                "title":"fa-chart-scatter",
                "searchTerms":[
                    "fa chart scatter"
                ]
            },
            {
                "title":"fa-check",
                "searchTerms":[
                    "fa check"
                ]
            },
            {
                "title":"fa-check-circle",
                "searchTerms":[
                    "fa check circle"
                ]
            },
            {
                "title":"fa-check-double",
                "searchTerms":[
                    "fa check double"
                ]
            },
            {
                "title":"fa-check-square",
                "searchTerms":[
                    "fa check square"
                ]
            },
            {
                "title":"fa-cheese",
                "searchTerms":[
                    "fa cheese"
                ]
            },
            {
                "title":"fa-cheese-swiss",
                "searchTerms":[
                    "fa cheese swiss"
                ]
            },
            {
                "title":"fa-cheeseburger",
                "searchTerms":[
                    "fa cheeseburger"
                ]
            },
            {
                "title":"fa-chess",
                "searchTerms":[
                    "fa chess"
                ]
            },
            {
                "title":"fa-chess-bishop",
                "searchTerms":[
                    "fa chess bishop"
                ]
            },
            {
                "title":"fa-chess-bishop-alt",
                "searchTerms":[
                    "fa chess bishop alt"
                ]
            },
            {
                "title":"fa-chess-board",
                "searchTerms":[
                    "fa chess board"
                ]
            },
            {
                "title":"fa-chess-clock",
                "searchTerms":[
                    "fa chess clock"
                ]
            },
            {
                "title":"fa-chess-clock-alt",
                "searchTerms":[
                    "fa chess clock alt"
                ]
            },
            {
                "title":"fa-chess-king",
                "searchTerms":[
                    "fa chess king"
                ]
            },
            {
                "title":"fa-chess-king-alt",
                "searchTerms":[
                    "fa chess king alt"
                ]
            },
            {
                "title":"fa-chess-knight",
                "searchTerms":[
                    "fa chess knight"
                ]
            },
            {
                "title":"fa-chess-knight-alt",
                "searchTerms":[
                    "fa chess knight alt"
                ]
            },
            {
                "title":"fa-chess-pawn",
                "searchTerms":[
                    "fa chess pawn"
                ]
            },
            {
                "title":"fa-chess-pawn-alt",
                "searchTerms":[
                    "fa chess pawn alt"
                ]
            },
            {
                "title":"fa-chess-queen",
                "searchTerms":[
                    "fa chess queen"
                ]
            },
            {
                "title":"fa-chess-queen-alt",
                "searchTerms":[
                    "fa chess queen alt"
                ]
            },
            {
                "title":"fa-chess-rook",
                "searchTerms":[
                    "fa chess rook"
                ]
            },
            {
                "title":"fa-chess-rook-alt",
                "searchTerms":[
                    "fa chess rook alt"
                ]
            },
            {
                "title":"fa-chevron-circle-down",
                "searchTerms":[
                    "fa chevron circle down"
                ]
            },
            {
                "title":"fa-chevron-circle-left",
                "searchTerms":[
                    "fa chevron circle left"
                ]
            },
            {
                "title":"fa-chevron-circle-right",
                "searchTerms":[
                    "fa chevron circle right"
                ]
            },
            {
                "title":"fa-chevron-circle-up",
                "searchTerms":[
                    "fa chevron circle up"
                ]
            },
            {
                "title":"fa-chevron-double-down",
                "searchTerms":[
                    "fa chevron double down"
                ]
            },
            {
                "title":"fa-chevron-double-left",
                "searchTerms":[
                    "fa chevron double left"
                ]
            },
            {
                "title":"fa-chevron-double-right",
                "searchTerms":[
                    "fa chevron double right"
                ]
            },
            {
                "title":"fa-chevron-double-up",
                "searchTerms":[
                    "fa chevron double up"
                ]
            },
            {
                "title":"fa-chevron-down",
                "searchTerms":[
                    "fa chevron down"
                ]
            },
            {
                "title":"fa-chevron-left",
                "searchTerms":[
                    "fa chevron left"
                ]
            },
            {
                "title":"fa-chevron-right",
                "searchTerms":[
                    "fa chevron right"
                ]
            },
            {
                "title":"fa-chevron-square-down",
                "searchTerms":[
                    "fa chevron square down"
                ]
            },
            {
                "title":"fa-chevron-square-left",
                "searchTerms":[
                    "fa chevron square left"
                ]
            },
            {
                "title":"fa-chevron-square-right",
                "searchTerms":[
                    "fa chevron square right"
                ]
            },
            {
                "title":"fa-chevron-square-up",
                "searchTerms":[
                    "fa chevron square up"
                ]
            },
            {
                "title":"fa-chevron-up",
                "searchTerms":[
                    "fa chevron up"
                ]
            },
            {
                "title":"fa-child",
                "searchTerms":[
                    "fa child"
                ]
            },
            {
                "title":"fa-chimney",
                "searchTerms":[
                    "fa chimney"
                ]
            },
            {
                "title":"fa-chrome",
                "searchTerms":[
                    "fa chrome"
                ]
            },
            {
                "title":"fa-chromecast",
                "searchTerms":[
                    "fa chromecast"
                ]
            },
            {
                "title":"fa-church",
                "searchTerms":[
                    "fa church"
                ]
            },
            {
                "title":"fa-circle",
                "searchTerms":[
                    "fa circle"
                ]
            },
            {
                "title":"fa-circle-notch",
                "searchTerms":[
                    "fa circle notch"
                ]
            },
            {
                "title":"fa-city",
                "searchTerms":[
                    "fa city"
                ]
            },
            {
                "title":"fa-clarinet",
                "searchTerms":[
                    "fa clarinet"
                ]
            },
            {
                "title":"fa-claw-marks",
                "searchTerms":[
                    "fa claw marks"
                ]
            },
            {
                "title":"fa-clinic-medical",
                "searchTerms":[
                    "fa clinic medical"
                ]
            },
            {
                "title":"fa-clipboard",
                "searchTerms":[
                    "fa clipboard"
                ]
            },
            {
                "title":"fa-clipboard-check",
                "searchTerms":[
                    "fa clipboard check"
                ]
            },
            {
                "title":"fa-clipboard-list",
                "searchTerms":[
                    "fa clipboard list"
                ]
            },
            {
                "title":"fa-clipboard-list-check",
                "searchTerms":[
                    "fa clipboard list check"
                ]
            },
            {
                "title":"fa-clipboard-prescription",
                "searchTerms":[
                    "fa clipboard prescription"
                ]
            },
            {
                "title":"fa-clipboard-user",
                "searchTerms":[
                    "fa clipboard user"
                ]
            },
            {
                "title":"fa-clock",
                "searchTerms":[
                    "fa clock"
                ]
            },
            {
                "title":"fa-clone",
                "searchTerms":[
                    "fa clone"
                ]
            },
            {
                "title":"fa-closed-captioning",
                "searchTerms":[
                    "fa closed captioning"
                ]
            },
            {
                "title":"fa-cloud",
                "searchTerms":[
                    "fa cloud"
                ]
            },
            {
                "title":"fa-cloud-download",
                "searchTerms":[
                    "fa cloud download"
                ]
            },
            {
                "title":"fa-cloud-download-alt",
                "searchTerms":[
                    "fa cloud download alt"
                ]
            },
            {
                "title":"fa-cloud-drizzle",
                "searchTerms":[
                    "fa cloud drizzle"
                ]
            },
            {
                "title":"fa-cloud-hail",
                "searchTerms":[
                    "fa cloud hail"
                ]
            },
            {
                "title":"fa-cloud-hail-mixed",
                "searchTerms":[
                    "fa cloud hail mixed"
                ]
            },
            {
                "title":"fa-cloud-meatball",
                "searchTerms":[
                    "fa cloud meatball"
                ]
            },
            {
                "title":"fa-cloud-moon",
                "searchTerms":[
                    "fa cloud moon"
                ]
            },
            {
                "title":"fa-cloud-moon-rain",
                "searchTerms":[
                    "fa cloud moon rain"
                ]
            },
            {
                "title":"fa-cloud-music",
                "searchTerms":[
                    "fa cloud music"
                ]
            },
            {
                "title":"fa-cloud-rain",
                "searchTerms":[
                    "fa cloud rain"
                ]
            },
            {
                "title":"fa-cloud-rainbow",
                "searchTerms":[
                    "fa cloud rainbow"
                ]
            },
            {
                "title":"fa-cloud-showers",
                "searchTerms":[
                    "fa cloud showers"
                ]
            },
            {
                "title":"fa-cloud-showers-heavy",
                "searchTerms":[
                    "fa cloud showers heavy"
                ]
            },
            {
                "title":"fa-cloud-sleet",
                "searchTerms":[
                    "fa cloud sleet"
                ]
            },
            {
                "title":"fa-cloud-snow",
                "searchTerms":[
                    "fa cloud snow"
                ]
            },
            {
                "title":"fa-cloud-sun",
                "searchTerms":[
                    "fa cloud sun"
                ]
            },
            {
                "title":"fa-cloud-sun-rain",
                "searchTerms":[
                    "fa cloud sun rain"
                ]
            },
            {
                "title":"fa-cloud-upload",
                "searchTerms":[
                    "fa cloud upload"
                ]
            },
            {
                "title":"fa-cloud-upload-alt",
                "searchTerms":[
                    "fa cloud upload alt"
                ]
            },
            {
                "title":"fa-clouds",
                "searchTerms":[
                    "fa clouds"
                ]
            },
            {
                "title":"fa-clouds-moon",
                "searchTerms":[
                    "fa clouds moon"
                ]
            },
            {
                "title":"fa-clouds-sun",
                "searchTerms":[
                    "fa clouds sun"
                ]
            },
            {
                "title":"fa-cloudscale",
                "searchTerms":[
                    "fa cloudscale"
                ]
            },
            {
                "title":"fa-cloudsmith",
                "searchTerms":[
                    "fa cloudsmith"
                ]
            },
            {
                "title":"fa-cloudversify",
                "searchTerms":[
                    "fa cloudversify"
                ]
            },
            {
                "title":"fa-club",
                "searchTerms":[
                    "fa club"
                ]
            },
            {
                "title":"fa-cocktail",
                "searchTerms":[
                    "fa cocktail"
                ]
            },
            {
                "title":"fa-code",
                "searchTerms":[
                    "fa code"
                ]
            },
            {
                "title":"fa-code-branch",
                "searchTerms":[
                    "fa code branch"
                ]
            },
            {
                "title":"fa-code-commit",
                "searchTerms":[
                    "fa code commit"
                ]
            },
            {
                "title":"fa-code-merge",
                "searchTerms":[
                    "fa code merge"
                ]
            },
            {
                "title":"fa-codepen",
                "searchTerms":[
                    "fa codepen"
                ]
            },
            {
                "title":"fa-codiepie",
                "searchTerms":[
                    "fa codiepie"
                ]
            },
            {
                "title":"fa-coffee",
                "searchTerms":[
                    "fa coffee"
                ]
            },
            {
                "title":"fa-coffee-togo",
                "searchTerms":[
                    "fa coffee togo"
                ]
            },
            {
                "title":"fa-coffin",
                "searchTerms":[
                    "fa coffin"
                ]
            },
            {
                "title":"fa-cog",
                "searchTerms":[
                    "fa cog"
                ]
            },
            {
                "title":"fa-cogs",
                "searchTerms":[
                    "fa cogs"
                ]
            },
            {
                "title":"fa-coin",
                "searchTerms":[
                    "fa coin"
                ]
            },
            {
                "title":"fa-coins",
                "searchTerms":[
                    "fa coins"
                ]
            },
            {
                "title":"fa-columns",
                "searchTerms":[
                    "fa columns"
                ]
            },
            {
                "title":"fa-comment",
                "searchTerms":[
                    "fa comment"
                ]
            },
            {
                "title":"fa-comment-alt",
                "searchTerms":[
                    "fa comment alt"
                ]
            },
            {
                "title":"fa-comment-alt-check",
                "searchTerms":[
                    "fa comment alt check"
                ]
            },
            {
                "title":"fa-comment-alt-dollar",
                "searchTerms":[
                    "fa comment alt dollar"
                ]
            },
            {
                "title":"fa-comment-alt-dots",
                "searchTerms":[
                    "fa comment alt dots"
                ]
            },
            {
                "title":"fa-comment-alt-edit",
                "searchTerms":[
                    "fa comment alt edit"
                ]
            },
            {
                "title":"fa-comment-alt-exclamation",
                "searchTerms":[
                    "fa comment alt exclamation"
                ]
            },
            {
                "title":"fa-comment-alt-lines",
                "searchTerms":[
                    "fa comment alt lines"
                ]
            },
            {
                "title":"fa-comment-alt-medical",
                "searchTerms":[
                    "fa comment alt medical"
                ]
            },
            {
                "title":"fa-comment-alt-minus",
                "searchTerms":[
                    "fa comment alt minus"
                ]
            },
            {
                "title":"fa-comment-alt-music",
                "searchTerms":[
                    "fa comment alt music"
                ]
            },
            {
                "title":"fa-comment-alt-plus",
                "searchTerms":[
                    "fa comment alt plus"
                ]
            },
            {
                "title":"fa-comment-alt-slash",
                "searchTerms":[
                    "fa comment alt slash"
                ]
            },
            {
                "title":"fa-comment-alt-smile",
                "searchTerms":[
                    "fa comment alt smile"
                ]
            },
            {
                "title":"fa-comment-alt-times",
                "searchTerms":[
                    "fa comment alt times"
                ]
            },
            {
                "title":"fa-comment-check",
                "searchTerms":[
                    "fa comment check"
                ]
            },
            {
                "title":"fa-comment-dollar",
                "searchTerms":[
                    "fa comment dollar"
                ]
            },
            {
                "title":"fa-comment-dots",
                "searchTerms":[
                    "fa comment dots"
                ]
            },
            {
                "title":"fa-comment-edit",
                "searchTerms":[
                    "fa comment edit"
                ]
            },
            {
                "title":"fa-comment-exclamation",
                "searchTerms":[
                    "fa comment exclamation"
                ]
            },
            {
                "title":"fa-comment-lines",
                "searchTerms":[
                    "fa comment lines"
                ]
            },
            {
                "title":"fa-comment-medical",
                "searchTerms":[
                    "fa comment medical"
                ]
            },
            {
                "title":"fa-comment-minus",
                "searchTerms":[
                    "fa comment minus"
                ]
            },
            {
                "title":"fa-comment-music",
                "searchTerms":[
                    "fa comment music"
                ]
            },
            {
                "title":"fa-comment-plus",
                "searchTerms":[
                    "fa comment plus"
                ]
            },
            {
                "title":"fa-comment-slash",
                "searchTerms":[
                    "fa comment slash"
                ]
            },
            {
                "title":"fa-comment-smile",
                "searchTerms":[
                    "fa comment smile"
                ]
            },
            {
                "title":"fa-comment-times",
                "searchTerms":[
                    "fa comment times"
                ]
            },
            {
                "title":"fa-comments",
                "searchTerms":[
                    "fa comments"
                ]
            },
            {
                "title":"fa-comments-alt",
                "searchTerms":[
                    "fa comments alt"
                ]
            },
            {
                "title":"fa-comments-alt-dollar",
                "searchTerms":[
                    "fa comments alt dollar"
                ]
            },
            {
                "title":"fa-comments-dollar",
                "searchTerms":[
                    "fa comments dollar"
                ]
            },
            {
                "title":"fa-compact-disc",
                "searchTerms":[
                    "fa compact disc"
                ]
            },
            {
                "title":"fa-compass",
                "searchTerms":[
                    "fa compass"
                ]
            },
            {
                "title":"fa-compass-slash",
                "searchTerms":[
                    "fa compass slash"
                ]
            },
            {
                "title":"fa-compress",
                "searchTerms":[
                    "fa compress"
                ]
            },
            {
                "title":"fa-compress-alt",
                "searchTerms":[
                    "fa compress alt"
                ]
            },
            {
                "title":"fa-compress-arrows-alt",
                "searchTerms":[
                    "fa compress arrows alt"
                ]
            },
            {
                "title":"fa-compress-wide",
                "searchTerms":[
                    "fa compress wide"
                ]
            },
            {
                "title":"fa-computer-classic",
                "searchTerms":[
                    "fa computer classic"
                ]
            },
            {
                "title":"fa-computer-speaker",
                "searchTerms":[
                    "fa computer speaker"
                ]
            },
            {
                "title":"fa-concierge-bell",
                "searchTerms":[
                    "fa concierge bell"
                ]
            },
            {
                "title":"fa-confluence",
                "searchTerms":[
                    "fa confluence"
                ]
            },
            {
                "title":"fa-connectdevelop",
                "searchTerms":[
                    "fa connectdevelop"
                ]
            },
            {
                "title":"fa-construction",
                "searchTerms":[
                    "fa construction"
                ]
            },
            {
                "title":"fa-container-storage",
                "searchTerms":[
                    "fa container storage"
                ]
            },
            {
                "title":"fa-contao",
                "searchTerms":[
                    "fa contao"
                ]
            },
            {
                "title":"fa-conveyor-belt",
                "searchTerms":[
                    "fa conveyor belt"
                ]
            },
            {
                "title":"fa-conveyor-belt-alt",
                "searchTerms":[
                    "fa conveyor belt alt"
                ]
            },
            {
                "title":"fa-cookie",
                "searchTerms":[
                    "fa cookie"
                ]
            },
            {
                "title":"fa-cookie-bite",
                "searchTerms":[
                    "fa cookie bite"
                ]
            },
            {
                "title":"fa-copy",
                "searchTerms":[
                    "fa copy"
                ]
            },
            {
                "title":"fa-copyright",
                "searchTerms":[
                    "fa copyright"
                ]
            },
            {
                "title":"fa-corn",
                "searchTerms":[
                    "fa corn"
                ]
            },
            {
                "title":"fa-cotton-bureau",
                "searchTerms":[
                    "fa cotton bureau"
                ]
            },
            {
                "title":"fa-couch",
                "searchTerms":[
                    "fa couch"
                ]
            },
            {
                "title":"fa-cow",
                "searchTerms":[
                    "fa cow"
                ]
            },
            {
                "title":"fa-cowbell",
                "searchTerms":[
                    "fa cowbell"
                ]
            },
            {
                "title":"fa-cowbell-more",
                "searchTerms":[
                    "fa cowbell more"
                ]
            },
            {
                "title":"fa-cpanel",
                "searchTerms":[
                    "fa cpanel"
                ]
            },
            {
                "title":"fa-creative-commons",
                "searchTerms":[
                    "fa creative commons"
                ]
            },
            {
                "title":"fa-creative-commons-by",
                "searchTerms":[
                    "fa creative commons by"
                ]
            },
            {
                "title":"fa-creative-commons-nc",
                "searchTerms":[
                    "fa creative commons nc"
                ]
            },
            {
                "title":"fa-creative-commons-nc-eu",
                "searchTerms":[
                    "fa creative commons nc eu"
                ]
            },
            {
                "title":"fa-creative-commons-nc-jp",
                "searchTerms":[
                    "fa creative commons nc jp"
                ]
            },
            {
                "title":"fa-creative-commons-nd",
                "searchTerms":[
                    "fa creative commons nd"
                ]
            },
            {
                "title":"fa-creative-commons-pd",
                "searchTerms":[
                    "fa creative commons pd"
                ]
            },
            {
                "title":"fa-creative-commons-pd-alt",
                "searchTerms":[
                    "fa creative commons pd alt"
                ]
            },
            {
                "title":"fa-creative-commons-remix",
                "searchTerms":[
                    "fa creative commons remix"
                ]
            },
            {
                "title":"fa-creative-commons-sa",
                "searchTerms":[
                    "fa creative commons sa"
                ]
            },
            {
                "title":"fa-creative-commons-sampling",
                "searchTerms":[
                    "fa creative commons sampling"
                ]
            },
            {
                "title":"fa-creative-commons-sampling-plus",
                "searchTerms":[
                    "fa creative commons sampling plus"
                ]
            },
            {
                "title":"fa-creative-commons-share",
                "searchTerms":[
                    "fa creative commons share"
                ]
            },
            {
                "title":"fa-creative-commons-zero",
                "searchTerms":[
                    "fa creative commons zero"
                ]
            },
            {
                "title":"fa-credit-card",
                "searchTerms":[
                    "fa credit card"
                ]
            },
            {
                "title":"fa-credit-card-blank",
                "searchTerms":[
                    "fa credit card blank"
                ]
            },
            {
                "title":"fa-credit-card-front",
                "searchTerms":[
                    "fa credit card front"
                ]
            },
            {
                "title":"fa-cricket",
                "searchTerms":[
                    "fa cricket"
                ]
            },
            {
                "title":"fa-critical-role",
                "searchTerms":[
                    "fa critical role"
                ]
            },
            {
                "title":"fa-croissant",
                "searchTerms":[
                    "fa croissant"
                ]
            },
            {
                "title":"fa-crop",
                "searchTerms":[
                    "fa crop"
                ]
            },
            {
                "title":"fa-crop-alt",
                "searchTerms":[
                    "fa crop alt"
                ]
            },
            {
                "title":"fa-cross",
                "searchTerms":[
                    "fa cross"
                ]
            },
            {
                "title":"fa-crosshairs",
                "searchTerms":[
                    "fa crosshairs"
                ]
            },
            {
                "title":"fa-crow",
                "searchTerms":[
                    "fa crow"
                ]
            },
            {
                "title":"fa-crown",
                "searchTerms":[
                    "fa crown"
                ]
            },
            {
                "title":"fa-crutch",
                "searchTerms":[
                    "fa crutch"
                ]
            },
            {
                "title":"fa-crutches",
                "searchTerms":[
                    "fa crutches"
                ]
            },
            {
                "title":"fa-css3",
                "searchTerms":[
                    "fa css3"
                ]
            },
            {
                "title":"fa-css3-alt",
                "searchTerms":[
                    "fa css3 alt"
                ]
            },
            {
                "title":"fa-cube",
                "searchTerms":[
                    "fa cube"
                ]
            },
            {
                "title":"fa-cubes",
                "searchTerms":[
                    "fa cubes"
                ]
            },
            {
                "title":"fa-curling",
                "searchTerms":[
                    "fa curling"
                ]
            },
            {
                "title":"fa-cut",
                "searchTerms":[
                    "fa cut"
                ]
            },
            {
                "title":"fa-cuttlefish",
                "searchTerms":[
                    "fa cuttlefish"
                ]
            },
            {
                "title":"fa-d-and-d",
                "searchTerms":[
                    "fa d and d"
                ]
            },
            {
                "title":"fa-d-and-d-beyond",
                "searchTerms":[
                    "fa d and d beyond"
                ]
            },
            {
                "title":"fa-dagger",
                "searchTerms":[
                    "fa dagger"
                ]
            },
            {
                "title":"fa-dashcube",
                "searchTerms":[
                    "fa dashcube"
                ]
            },
            {
                "title":"fa-database",
                "searchTerms":[
                    "fa database"
                ]
            },
            {
                "title":"fa-deaf",
                "searchTerms":[
                    "fa deaf"
                ]
            },
            {
                "title":"fa-debug",
                "searchTerms":[
                    "fa debug"
                ]
            },
            {
                "title":"fa-deer",
                "searchTerms":[
                    "fa deer"
                ]
            },
            {
                "title":"fa-deer-rudolph",
                "searchTerms":[
                    "fa deer rudolph"
                ]
            },
            {
                "title":"fa-delicious",
                "searchTerms":[
                    "fa delicious"
                ]
            },
            {
                "title":"fa-democrat",
                "searchTerms":[
                    "fa democrat"
                ]
            },
            {
                "title":"fa-deploydog",
                "searchTerms":[
                    "fa deploydog"
                ]
            },
            {
                "title":"fa-deskpro",
                "searchTerms":[
                    "fa deskpro"
                ]
            },
            {
                "title":"fa-desktop",
                "searchTerms":[
                    "fa desktop"
                ]
            },
            {
                "title":"fa-desktop-alt",
                "searchTerms":[
                    "fa desktop alt"
                ]
            },
            {
                "title":"fa-dev",
                "searchTerms":[
                    "fa dev"
                ]
            },
            {
                "title":"fa-deviantart",
                "searchTerms":[
                    "fa deviantart"
                ]
            },
            {
                "title":"fa-dewpoint",
                "searchTerms":[
                    "fa dewpoint"
                ]
            },
            {
                "title":"fa-dharmachakra",
                "searchTerms":[
                    "fa dharmachakra"
                ]
            },
            {
                "title":"fa-dhl",
                "searchTerms":[
                    "fa dhl"
                ]
            },
            {
                "title":"fa-diagnoses",
                "searchTerms":[
                    "fa diagnoses"
                ]
            },
            {
                "title":"fa-diamond",
                "searchTerms":[
                    "fa diamond"
                ]
            },
            {
                "title":"fa-diaspora",
                "searchTerms":[
                    "fa diaspora"
                ]
            },
            {
                "title":"fa-dice",
                "searchTerms":[
                    "fa dice"
                ]
            },
            {
                "title":"fa-dice-d10",
                "searchTerms":[
                    "fa dice d10"
                ]
            },
            {
                "title":"fa-dice-d12",
                "searchTerms":[
                    "fa dice d12"
                ]
            },
            {
                "title":"fa-dice-d20",
                "searchTerms":[
                    "fa dice d20"
                ]
            },
            {
                "title":"fa-dice-d4",
                "searchTerms":[
                    "fa dice d4"
                ]
            },
            {
                "title":"fa-dice-d6",
                "searchTerms":[
                    "fa dice d6"
                ]
            },
            {
                "title":"fa-dice-d8",
                "searchTerms":[
                    "fa dice d8"
                ]
            },
            {
                "title":"fa-dice-five",
                "searchTerms":[
                    "fa dice five"
                ]
            },
            {
                "title":"fa-dice-four",
                "searchTerms":[
                    "fa dice four"
                ]
            },
            {
                "title":"fa-dice-one",
                "searchTerms":[
                    "fa dice one"
                ]
            },
            {
                "title":"fa-dice-six",
                "searchTerms":[
                    "fa dice six"
                ]
            },
            {
                "title":"fa-dice-three",
                "searchTerms":[
                    "fa dice three"
                ]
            },
            {
                "title":"fa-dice-two",
                "searchTerms":[
                    "fa dice two"
                ]
            },
            {
                "title":"fa-digg",
                "searchTerms":[
                    "fa digg"
                ]
            },
            {
                "title":"fa-digging",
                "searchTerms":[
                    "fa digging"
                ]
            },
            {
                "title":"fa-digital-ocean",
                "searchTerms":[
                    "fa digital ocean"
                ]
            },
            {
                "title":"fa-digital-tachograph",
                "searchTerms":[
                    "fa digital tachograph"
                ]
            },
            {
                "title":"fa-diploma",
                "searchTerms":[
                    "fa diploma"
                ]
            },
            {
                "title":"fa-directions",
                "searchTerms":[
                    "fa directions"
                ]
            },
            {
                "title":"fa-disc-drive",
                "searchTerms":[
                    "fa disc drive"
                ]
            },
            {
                "title":"fa-discord",
                "searchTerms":[
                    "fa discord"
                ]
            },
            {
                "title":"fa-discourse",
                "searchTerms":[
                    "fa discourse"
                ]
            },
            {
                "title":"fa-disease",
                "searchTerms":[
                    "fa disease"
                ]
            },
            {
                "title":"fa-divide",
                "searchTerms":[
                    "fa divide"
                ]
            },
            {
                "title":"fa-dizzy",
                "searchTerms":[
                    "fa dizzy"
                ]
            },
            {
                "title":"fa-dna",
                "searchTerms":[
                    "fa dna"
                ]
            },
            {
                "title":"fa-do-not-enter",
                "searchTerms":[
                    "fa do not enter"
                ]
            },
            {
                "title":"fa-dochub",
                "searchTerms":[
                    "fa dochub"
                ]
            },
            {
                "title":"fa-docker",
                "searchTerms":[
                    "fa docker"
                ]
            },
            {
                "title":"fa-dog",
                "searchTerms":[
                    "fa dog"
                ]
            },
            {
                "title":"fa-dog-leashed",
                "searchTerms":[
                    "fa dog leashed"
                ]
            },
            {
                "title":"fa-dollar-sign",
                "searchTerms":[
                    "fa dollar sign"
                ]
            },
            {
                "title":"fa-dolly",
                "searchTerms":[
                    "fa dolly"
                ]
            },
            {
                "title":"fa-dolly-empty",
                "searchTerms":[
                    "fa dolly empty"
                ]
            },
            {
                "title":"fa-dolly-flatbed",
                "searchTerms":[
                    "fa dolly flatbed"
                ]
            },
            {
                "title":"fa-dolly-flatbed-alt",
                "searchTerms":[
                    "fa dolly flatbed alt"
                ]
            },
            {
                "title":"fa-dolly-flatbed-empty",
                "searchTerms":[
                    "fa dolly flatbed empty"
                ]
            },
            {
                "title":"fa-donate",
                "searchTerms":[
                    "fa donate"
                ]
            },
            {
                "title":"fa-door-closed",
                "searchTerms":[
                    "fa door closed"
                ]
            },
            {
                "title":"fa-door-open",
                "searchTerms":[
                    "fa door open"
                ]
            },
            {
                "title":"fa-dot-circle",
                "searchTerms":[
                    "fa dot circle"
                ]
            },
            {
                "title":"fa-dove",
                "searchTerms":[
                    "fa dove"
                ]
            },
            {
                "title":"fa-download",
                "searchTerms":[
                    "fa download"
                ]
            },
            {
                "title":"fa-draft2digital",
                "searchTerms":[
                    "fa draft2digital"
                ]
            },
            {
                "title":"fa-drafting-compass",
                "searchTerms":[
                    "fa drafting compass"
                ]
            },
            {
                "title":"fa-dragon",
                "searchTerms":[
                    "fa dragon"
                ]
            },
            {
                "title":"fa-draw-circle",
                "searchTerms":[
                    "fa draw circle"
                ]
            },
            {
                "title":"fa-draw-polygon",
                "searchTerms":[
                    "fa draw polygon"
                ]
            },
            {
                "title":"fa-draw-square",
                "searchTerms":[
                    "fa draw square"
                ]
            },
            {
                "title":"fa-dreidel",
                "searchTerms":[
                    "fa dreidel"
                ]
            },
            {
                "title":"fa-dribbble",
                "searchTerms":[
                    "fa dribbble"
                ]
            },
            {
                "title":"fa-dribbble-square",
                "searchTerms":[
                    "fa dribbble square"
                ]
            },
            {
                "title":"fa-drone",
                "searchTerms":[
                    "fa drone"
                ]
            },
            {
                "title":"fa-drone-alt",
                "searchTerms":[
                    "fa drone alt"
                ]
            },
            {
                "title":"fa-dropbox",
                "searchTerms":[
                    "fa dropbox"
                ]
            },
            {
                "title":"fa-drum",
                "searchTerms":[
                    "fa drum"
                ]
            },
            {
                "title":"fa-drum-steelpan",
                "searchTerms":[
                    "fa drum steelpan"
                ]
            },
            {
                "title":"fa-drumstick",
                "searchTerms":[
                    "fa drumstick"
                ]
            },
            {
                "title":"fa-drumstick-bite",
                "searchTerms":[
                    "fa drumstick bite"
                ]
            },
            {
                "title":"fa-drupal",
                "searchTerms":[
                    "fa drupal"
                ]
            },
            {
                "title":"fa-dryer",
                "searchTerms":[
                    "fa dryer"
                ]
            },
            {
                "title":"fa-dryer-alt",
                "searchTerms":[
                    "fa dryer alt"
                ]
            },
            {
                "title":"fa-duck",
                "searchTerms":[
                    "fa duck"
                ]
            },
            {
                "title":"fa-dumbbell",
                "searchTerms":[
                    "fa dumbbell"
                ]
            },
            {
                "title":"fa-dumpster",
                "searchTerms":[
                    "fa dumpster"
                ]
            },
            {
                "title":"fa-dumpster-fire",
                "searchTerms":[
                    "fa dumpster fire"
                ]
            },
            {
                "title":"fa-dungeon",
                "searchTerms":[
                    "fa dungeon"
                ]
            },
            {
                "title":"fa-dyalog",
                "searchTerms":[
                    "fa dyalog"
                ]
            },
            {
                "title":"fa-ear",
                "searchTerms":[
                    "fa ear"
                ]
            },
            {
                "title":"fa-ear-muffs",
                "searchTerms":[
                    "fa ear muffs"
                ]
            },
            {
                "title":"fa-earlybirds",
                "searchTerms":[
                    "fa earlybirds"
                ]
            },
            {
                "title":"fa-ebay",
                "searchTerms":[
                    "fa ebay"
                ]
            },
            {
                "title":"fa-eclipse",
                "searchTerms":[
                    "fa eclipse"
                ]
            },
            {
                "title":"fa-eclipse-alt",
                "searchTerms":[
                    "fa eclipse alt"
                ]
            },
            {
                "title":"fa-edge",
                "searchTerms":[
                    "fa edge"
                ]
            },
            {
                "title":"fa-edit",
                "searchTerms":[
                    "fa edit"
                ]
            },
            {
                "title":"fa-egg",
                "searchTerms":[
                    "fa egg"
                ]
            },
            {
                "title":"fa-egg-fried",
                "searchTerms":[
                    "fa egg fried"
                ]
            },
            {
                "title":"fa-eject",
                "searchTerms":[
                    "fa eject"
                ]
            },
            {
                "title":"fa-elementor",
                "searchTerms":[
                    "fa elementor"
                ]
            },
            {
                "title":"fa-elephant",
                "searchTerms":[
                    "fa elephant"
                ]
            },
            {
                "title":"fa-ellipsis-h",
                "searchTerms":[
                    "fa ellipsis h"
                ]
            },
            {
                "title":"fa-ellipsis-h-alt",
                "searchTerms":[
                    "fa ellipsis h alt"
                ]
            },
            {
                "title":"fa-ellipsis-v",
                "searchTerms":[
                    "fa ellipsis v"
                ]
            },
            {
                "title":"fa-ellipsis-v-alt",
                "searchTerms":[
                    "fa ellipsis v alt"
                ]
            },
            {
                "title":"fa-ello",
                "searchTerms":[
                    "fa ello"
                ]
            },
            {
                "title":"fa-ember",
                "searchTerms":[
                    "fa ember"
                ]
            },
            {
                "title":"fa-empire",
                "searchTerms":[
                    "fa empire"
                ]
            },
            {
                "title":"fa-empty-set",
                "searchTerms":[
                    "fa empty set"
                ]
            },
            {
                "title":"fa-engine-warning",
                "searchTerms":[
                    "fa engine warning"
                ]
            },
            {
                "title":"fa-envelope",
                "searchTerms":[
                    "fa envelope"
                ]
            },
            {
                "title":"fa-envelope-open",
                "searchTerms":[
                    "fa envelope open"
                ]
            },
            {
                "title":"fa-envelope-open-dollar",
                "searchTerms":[
                    "fa envelope open dollar"
                ]
            },
            {
                "title":"fa-envelope-open-text",
                "searchTerms":[
                    "fa envelope open text"
                ]
            },
            {
                "title":"fa-envelope-square",
                "searchTerms":[
                    "fa envelope square"
                ]
            },
            {
                "title":"fa-envira",
                "searchTerms":[
                    "fa envira"
                ]
            },
            {
                "title":"fa-equals",
                "searchTerms":[
                    "fa equals"
                ]
            },
            {
                "title":"fa-eraser",
                "searchTerms":[
                    "fa eraser"
                ]
            },
            {
                "title":"fa-erlang",
                "searchTerms":[
                    "fa erlang"
                ]
            },
            {
                "title":"fa-ethereum",
                "searchTerms":[
                    "fa ethereum"
                ]
            },
            {
                "title":"fa-ethernet",
                "searchTerms":[
                    "fa ethernet"
                ]
            },
            {
                "title":"fa-etsy",
                "searchTerms":[
                    "fa etsy"
                ]
            },
            {
                "title":"fa-euro-sign",
                "searchTerms":[
                    "fa euro sign"
                ]
            },
            {
                "title":"fa-evernote",
                "searchTerms":[
                    "fa evernote"
                ]
            },
            {
                "title":"fa-exchange",
                "searchTerms":[
                    "fa exchange"
                ]
            },
            {
                "title":"fa-exchange-alt",
                "searchTerms":[
                    "fa exchange alt"
                ]
            },
            {
                "title":"fa-exclamation",
                "searchTerms":[
                    "fa exclamation"
                ]
            },
            {
                "title":"fa-exclamation-circle",
                "searchTerms":[
                    "fa exclamation circle"
                ]
            },
            {
                "title":"fa-exclamation-square",
                "searchTerms":[
                    "fa exclamation square"
                ]
            },
            {
                "title":"fa-exclamation-triangle",
                "searchTerms":[
                    "fa exclamation triangle"
                ]
            },
            {
                "title":"fa-expand",
                "searchTerms":[
                    "fa expand"
                ]
            },
            {
                "title":"fa-expand-alt",
                "searchTerms":[
                    "fa expand alt"
                ]
            },
            {
                "title":"fa-expand-arrows",
                "searchTerms":[
                    "fa expand arrows"
                ]
            },
            {
                "title":"fa-expand-arrows-alt",
                "searchTerms":[
                    "fa expand arrows alt"
                ]
            },
            {
                "title":"fa-expand-wide",
                "searchTerms":[
                    "fa expand wide"
                ]
            },
            {
                "title":"fa-expeditedssl",
                "searchTerms":[
                    "fa expeditedssl"
                ]
            },
            {
                "title":"fa-external-link",
                "searchTerms":[
                    "fa external link"
                ]
            },
            {
                "title":"fa-external-link-alt",
                "searchTerms":[
                    "fa external link alt"
                ]
            },
            {
                "title":"fa-external-link-square",
                "searchTerms":[
                    "fa external link square"
                ]
            },
            {
                "title":"fa-external-link-square-alt",
                "searchTerms":[
                    "fa external link square alt"
                ]
            },
            {
                "title":"fa-eye",
                "searchTerms":[
                    "fa eye"
                ]
            },
            {
                "title":"fa-eye-dropper",
                "searchTerms":[
                    "fa eye dropper"
                ]
            },
            {
                "title":"fa-eye-evil",
                "searchTerms":[
                    "fa eye evil"
                ]
            },
            {
                "title":"fa-eye-slash",
                "searchTerms":[
                    "fa eye slash"
                ]
            },
            {
                "title":"fa-facebook",
                "searchTerms":[
                    "fa facebook"
                ]
            },
            {
                "title":"fa-facebook-f",
                "searchTerms":[
                    "fa facebook f"
                ]
            },
            {
                "title":"fa-facebook-messenger",
                "searchTerms":[
                    "fa facebook messenger"
                ]
            },
            {
                "title":"fa-facebook-square",
                "searchTerms":[
                    "fa facebook square"
                ]
            },
            {
                "title":"fa-fan",
                "searchTerms":[
                    "fa fan"
                ]
            },
            {
                "title":"fa-fantasy-flight-games",
                "searchTerms":[
                    "fa fantasy flight games"
                ]
            },
            {
                "title":"fa-farm",
                "searchTerms":[
                    "fa farm"
                ]
            },
            {
                "title":"fa-fast-backward",
                "searchTerms":[
                    "fa fast backward"
                ]
            },
            {
                "title":"fa-fast-forward",
                "searchTerms":[
                    "fa fast forward"
                ]
            },
            {
                "title":"fa-fax",
                "searchTerms":[
                    "fa fax"
                ]
            },
            {
                "title":"fa-feather",
                "searchTerms":[
                    "fa feather"
                ]
            },
            {
                "title":"fa-feather-alt",
                "searchTerms":[
                    "fa feather alt"
                ]
            },
            {
                "title":"fa-fedex",
                "searchTerms":[
                    "fa fedex"
                ]
            },
            {
                "title":"fa-fedora",
                "searchTerms":[
                    "fa fedora"
                ]
            },
            {
                "title":"fa-female",
                "searchTerms":[
                    "fa female"
                ]
            },
            {
                "title":"fa-field-hockey",
                "searchTerms":[
                    "fa field hockey"
                ]
            },
            {
                "title":"fa-fighter-jet",
                "searchTerms":[
                    "fa fighter jet"
                ]
            },
            {
                "title":"fa-figma",
                "searchTerms":[
                    "fa figma"
                ]
            },
            {
                "title":"fa-file",
                "searchTerms":[
                    "fa file"
                ]
            },
            {
                "title":"fa-file-alt",
                "searchTerms":[
                    "fa file alt"
                ]
            },
            {
                "title":"fa-file-archive",
                "searchTerms":[
                    "fa file archive"
                ]
            },
            {
                "title":"fa-file-audio",
                "searchTerms":[
                    "fa file audio"
                ]
            },
            {
                "title":"fa-file-certificate",
                "searchTerms":[
                    "fa file certificate"
                ]
            },
            {
                "title":"fa-file-chart-line",
                "searchTerms":[
                    "fa file chart line"
                ]
            },
            {
                "title":"fa-file-chart-pie",
                "searchTerms":[
                    "fa file chart pie"
                ]
            },
            {
                "title":"fa-file-check",
                "searchTerms":[
                    "fa file check"
                ]
            },
            {
                "title":"fa-file-code",
                "searchTerms":[
                    "fa file code"
                ]
            },
            {
                "title":"fa-file-contract",
                "searchTerms":[
                    "fa file contract"
                ]
            },
            {
                "title":"fa-file-csv",
                "searchTerms":[
                    "fa file csv"
                ]
            },
            {
                "title":"fa-file-download",
                "searchTerms":[
                    "fa file download"
                ]
            },
            {
                "title":"fa-file-edit",
                "searchTerms":[
                    "fa file edit"
                ]
            },
            {
                "title":"fa-file-excel",
                "searchTerms":[
                    "fa file excel"
                ]
            },
            {
                "title":"fa-file-exclamation",
                "searchTerms":[
                    "fa file exclamation"
                ]
            },
            {
                "title":"fa-file-export",
                "searchTerms":[
                    "fa file export"
                ]
            },
            {
                "title":"fa-file-image",
                "searchTerms":[
                    "fa file image"
                ]
            },
            {
                "title":"fa-file-import",
                "searchTerms":[
                    "fa file import"
                ]
            },
            {
                "title":"fa-file-invoice",
                "searchTerms":[
                    "fa file invoice"
                ]
            },
            {
                "title":"fa-file-invoice-dollar",
                "searchTerms":[
                    "fa file invoice dollar"
                ]
            },
            {
                "title":"fa-file-medical",
                "searchTerms":[
                    "fa file medical"
                ]
            },
            {
                "title":"fa-file-medical-alt",
                "searchTerms":[
                    "fa file medical alt"
                ]
            },
            {
                "title":"fa-file-minus",
                "searchTerms":[
                    "fa file minus"
                ]
            },
            {
                "title":"fa-file-music",
                "searchTerms":[
                    "fa file music"
                ]
            },
            {
                "title":"fa-file-pdf",
                "searchTerms":[
                    "fa file pdf"
                ]
            },
            {
                "title":"fa-file-plus",
                "searchTerms":[
                    "fa file plus"
                ]
            },
            {
                "title":"fa-file-powerpoint",
                "searchTerms":[
                    "fa file powerpoint"
                ]
            },
            {
                "title":"fa-file-prescription",
                "searchTerms":[
                    "fa file prescription"
                ]
            },
            {
                "title":"fa-file-search",
                "searchTerms":[
                    "fa file search"
                ]
            },
            {
                "title":"fa-file-signature",
                "searchTerms":[
                    "fa file signature"
                ]
            },
            {
                "title":"fa-file-spreadsheet",
                "searchTerms":[
                    "fa file spreadsheet"
                ]
            },
            {
                "title":"fa-file-times",
                "searchTerms":[
                    "fa file times"
                ]
            },
            {
                "title":"fa-file-upload",
                "searchTerms":[
                    "fa file upload"
                ]
            },
            {
                "title":"fa-file-user",
                "searchTerms":[
                    "fa file user"
                ]
            },
            {
                "title":"fa-file-video",
                "searchTerms":[
                    "fa file video"
                ]
            },
            {
                "title":"fa-file-word",
                "searchTerms":[
                    "fa file word"
                ]
            },
            {
                "title":"fa-files-medical",
                "searchTerms":[
                    "fa files medical"
                ]
            },
            {
                "title":"fa-fill",
                "searchTerms":[
                    "fa fill"
                ]
            },
            {
                "title":"fa-fill-drip",
                "searchTerms":[
                    "fa fill drip"
                ]
            },
            {
                "title":"fa-film",
                "searchTerms":[
                    "fa film"
                ]
            },
            {
                "title":"fa-film-alt",
                "searchTerms":[
                    "fa film alt"
                ]
            },
            {
                "title":"fa-film-canister",
                "searchTerms":[
                    "fa film canister"
                ]
            },
            {
                "title":"fa-filter",
                "searchTerms":[
                    "fa filter"
                ]
            },
            {
                "title":"fa-fingerprint",
                "searchTerms":[
                    "fa fingerprint"
                ]
            },
            {
                "title":"fa-fire",
                "searchTerms":[
                    "fa fire"
                ]
            },
            {
                "title":"fa-fire-alt",
                "searchTerms":[
                    "fa fire alt"
                ]
            },
            {
                "title":"fa-fire-extinguisher",
                "searchTerms":[
                    "fa fire extinguisher"
                ]
            },
            {
                "title":"fa-fire-smoke",
                "searchTerms":[
                    "fa fire smoke"
                ]
            },
            {
                "title":"fa-firefox",
                "searchTerms":[
                    "fa firefox"
                ]
            },
            {
                "title":"fa-fireplace",
                "searchTerms":[
                    "fa fireplace"
                ]
            },
            {
                "title":"fa-first-aid",
                "searchTerms":[
                    "fa first aid"
                ]
            },
            {
                "title":"fa-first-order",
                "searchTerms":[
                    "fa first order"
                ]
            },
            {
                "title":"fa-first-order-alt",
                "searchTerms":[
                    "fa first order alt"
                ]
            },
            {
                "title":"fa-firstdraft",
                "searchTerms":[
                    "fa firstdraft"
                ]
            },
            {
                "title":"fa-fish",
                "searchTerms":[
                    "fa fish"
                ]
            },
            {
                "title":"fa-fish-cooked",
                "searchTerms":[
                    "fa fish cooked"
                ]
            },
            {
                "title":"fa-fist-raised",
                "searchTerms":[
                    "fa fist raised"
                ]
            },
            {
                "title":"fa-flag",
                "searchTerms":[
                    "fa flag"
                ]
            },
            {
                "title":"fa-flag-alt",
                "searchTerms":[
                    "fa flag alt"
                ]
            },
            {
                "title":"fa-flag-checkered",
                "searchTerms":[
                    "fa flag checkered"
                ]
            },
            {
                "title":"fa-flag-usa",
                "searchTerms":[
                    "fa flag usa"
                ]
            },
            {
                "title":"fa-flame",
                "searchTerms":[
                    "fa flame"
                ]
            },
            {
                "title":"fa-flashlight",
                "searchTerms":[
                    "fa flashlight"
                ]
            },
            {
                "title":"fa-flask",
                "searchTerms":[
                    "fa flask"
                ]
            },
            {
                "title":"fa-flask-poison",
                "searchTerms":[
                    "fa flask poison"
                ]
            },
            {
                "title":"fa-flask-potion",
                "searchTerms":[
                    "fa flask potion"
                ]
            },
            {
                "title":"fa-flickr",
                "searchTerms":[
                    "fa flickr"
                ]
            },
            {
                "title":"fa-flipboard",
                "searchTerms":[
                    "fa flipboard"
                ]
            },
            {
                "title":"fa-flower",
                "searchTerms":[
                    "fa flower"
                ]
            },
            {
                "title":"fa-flower-daffodil",
                "searchTerms":[
                    "fa flower daffodil"
                ]
            },
            {
                "title":"fa-flower-tulip",
                "searchTerms":[
                    "fa flower tulip"
                ]
            },
            {
                "title":"fa-flushed",
                "searchTerms":[
                    "fa flushed"
                ]
            },
            {
                "title":"fa-flute",
                "searchTerms":[
                    "fa flute"
                ]
            },
            {
                "title":"fa-flux-capacitor",
                "searchTerms":[
                    "fa flux capacitor"
                ]
            },
            {
                "title":"fa-fly",
                "searchTerms":[
                    "fa fly"
                ]
            },
            {
                "title":"fa-fog",
                "searchTerms":[
                    "fa fog"
                ]
            },
            {
                "title":"fa-folder",
                "searchTerms":[
                    "fa folder"
                ]
            },
            {
                "title":"fa-folder-minus",
                "searchTerms":[
                    "fa folder minus"
                ]
            },
            {
                "title":"fa-folder-open",
                "searchTerms":[
                    "fa folder open"
                ]
            },
            {
                "title":"fa-folder-plus",
                "searchTerms":[
                    "fa folder plus"
                ]
            },
            {
                "title":"fa-folder-times",
                "searchTerms":[
                    "fa folder times"
                ]
            },
            {
                "title":"fa-folder-tree",
                "searchTerms":[
                    "fa folder tree"
                ]
            },
            {
                "title":"fa-folders",
                "searchTerms":[
                    "fa folders"
                ]
            },
            {
                "title":"fa-font",
                "searchTerms":[
                    "fa font"
                ]
            },
            {
                "title":"fa-font-awesome",
                "searchTerms":[
                    "fa font awesome"
                ]
            },
            {
                "title":"fa-font-awesome-alt",
                "searchTerms":[
                    "fa font awesome alt"
                ]
            },
            {
                "title":"fa-font-awesome-flag",
                "searchTerms":[
                    "fa font awesome flag"
                ]
            },
            {
                "title":"fa-font-awesome-logo-full",
                "searchTerms":[
                    "fa font awesome logo full"
                ]
            },
            {
                "title":"fa-font-case",
                "searchTerms":[
                    "fa font case"
                ]
            },
            {
                "title":"fa-fonticons",
                "searchTerms":[
                    "fa fonticons"
                ]
            },
            {
                "title":"fa-fonticons-fi",
                "searchTerms":[
                    "fa fonticons fi"
                ]
            },
            {
                "title":"fa-football-ball",
                "searchTerms":[
                    "fa football ball"
                ]
            },
            {
                "title":"fa-football-helmet",
                "searchTerms":[
                    "fa football helmet"
                ]
            },
            {
                "title":"fa-forklift",
                "searchTerms":[
                    "fa forklift"
                ]
            },
            {
                "title":"fa-fort-awesome",
                "searchTerms":[
                    "fa fort awesome"
                ]
            },
            {
                "title":"fa-fort-awesome-alt",
                "searchTerms":[
                    "fa fort awesome alt"
                ]
            },
            {
                "title":"fa-forumbee",
                "searchTerms":[
                    "fa forumbee"
                ]
            },
            {
                "title":"fa-forward",
                "searchTerms":[
                    "fa forward"
                ]
            },
            {
                "title":"fa-foursquare",
                "searchTerms":[
                    "fa foursquare"
                ]
            },
            {
                "title":"fa-fragile",
                "searchTerms":[
                    "fa fragile"
                ]
            },
            {
                "title":"fa-free-code-camp",
                "searchTerms":[
                    "fa free code camp"
                ]
            },
            {
                "title":"fa-freebsd",
                "searchTerms":[
                    "fa freebsd"
                ]
            },
            {
                "title":"fa-french-fries",
                "searchTerms":[
                    "fa french fries"
                ]
            },
            {
                "title":"fa-frog",
                "searchTerms":[
                    "fa frog"
                ]
            },
            {
                "title":"fa-frosty-head",
                "searchTerms":[
                    "fa frosty head"
                ]
            },
            {
                "title":"fa-frown",
                "searchTerms":[
                    "fa frown"
                ]
            },
            {
                "title":"fa-frown-open",
                "searchTerms":[
                    "fa frown open"
                ]
            },
            {
                "title":"fa-fulcrum",
                "searchTerms":[
                    "fa fulcrum"
                ]
            },
            {
                "title":"fa-function",
                "searchTerms":[
                    "fa function"
                ]
            },
            {
                "title":"fa-funnel-dollar",
                "searchTerms":[
                    "fa funnel dollar"
                ]
            },
            {
                "title":"fa-futbol",
                "searchTerms":[
                    "fa futbol"
                ]
            },
            {
                "title":"fa-galactic-republic",
                "searchTerms":[
                    "fa galactic republic"
                ]
            },
            {
                "title":"fa-galactic-senate",
                "searchTerms":[
                    "fa galactic senate"
                ]
            },
            {
                "title":"fa-game-board",
                "searchTerms":[
                    "fa game board"
                ]
            },
            {
                "title":"fa-game-board-alt",
                "searchTerms":[
                    "fa game board alt"
                ]
            },
            {
                "title":"fa-game-console-handheld",
                "searchTerms":[
                    "fa game console handheld"
                ]
            },
            {
                "title":"fa-gamepad",
                "searchTerms":[
                    "fa gamepad"
                ]
            },
            {
                "title":"fa-gamepad-alt",
                "searchTerms":[
                    "fa gamepad alt"
                ]
            },
            {
                "title":"fa-gas-pump",
                "searchTerms":[
                    "fa gas pump"
                ]
            },
            {
                "title":"fa-gas-pump-slash",
                "searchTerms":[
                    "fa gas pump slash"
                ]
            },
            {
                "title":"fa-gavel",
                "searchTerms":[
                    "fa gavel"
                ]
            },
            {
                "title":"fa-gem",
                "searchTerms":[
                    "fa gem"
                ]
            },
            {
                "title":"fa-genderless",
                "searchTerms":[
                    "fa genderless"
                ]
            },
            {
                "title":"fa-get-pocket",
                "searchTerms":[
                    "fa get pocket"
                ]
            },
            {
                "title":"fa-gg",
                "searchTerms":[
                    "fa gg"
                ]
            },
            {
                "title":"fa-gg-circle",
                "searchTerms":[
                    "fa gg circle"
                ]
            },
            {
                "title":"fa-ghost",
                "searchTerms":[
                    "fa ghost"
                ]
            },
            {
                "title":"fa-gift",
                "searchTerms":[
                    "fa gift"
                ]
            },
            {
                "title":"fa-gift-card",
                "searchTerms":[
                    "fa gift card"
                ]
            },
            {
                "title":"fa-gifts",
                "searchTerms":[
                    "fa gifts"
                ]
            },
            {
                "title":"fa-gingerbread-man",
                "searchTerms":[
                    "fa gingerbread man"
                ]
            },
            {
                "title":"fa-git",
                "searchTerms":[
                    "fa git"
                ]
            },
            {
                "title":"fa-git-alt",
                "searchTerms":[
                    "fa git alt"
                ]
            },
            {
                "title":"fa-git-square",
                "searchTerms":[
                    "fa git square"
                ]
            },
            {
                "title":"fa-github",
                "searchTerms":[
                    "fa github"
                ]
            },
            {
                "title":"fa-github-alt",
                "searchTerms":[
                    "fa github alt"
                ]
            },
            {
                "title":"fa-github-square",
                "searchTerms":[
                    "fa github square"
                ]
            },
            {
                "title":"fa-gitkraken",
                "searchTerms":[
                    "fa gitkraken"
                ]
            },
            {
                "title":"fa-gitlab",
                "searchTerms":[
                    "fa gitlab"
                ]
            },
            {
                "title":"fa-gitter",
                "searchTerms":[
                    "fa gitter"
                ]
            },
            {
                "title":"fa-glass",
                "searchTerms":[
                    "fa glass"
                ]
            },
            {
                "title":"fa-glass-champagne",
                "searchTerms":[
                    "fa glass champagne"
                ]
            },
            {
                "title":"fa-glass-cheers",
                "searchTerms":[
                    "fa glass cheers"
                ]
            },
            {
                "title":"fa-glass-citrus",
                "searchTerms":[
                    "fa glass citrus"
                ]
            },
            {
                "title":"fa-glass-martini",
                "searchTerms":[
                    "fa glass martini"
                ]
            },
            {
                "title":"fa-glass-martini-alt",
                "searchTerms":[
                    "fa glass martini alt"
                ]
            },
            {
                "title":"fa-glass-whiskey",
                "searchTerms":[
                    "fa glass whiskey"
                ]
            },
            {
                "title":"fa-glass-whiskey-rocks",
                "searchTerms":[
                    "fa glass whiskey rocks"
                ]
            },
            {
                "title":"fa-glasses",
                "searchTerms":[
                    "fa glasses"
                ]
            },
            {
                "title":"fa-glasses-alt",
                "searchTerms":[
                    "fa glasses alt"
                ]
            },
            {
                "title":"fa-glide",
                "searchTerms":[
                    "fa glide"
                ]
            },
            {
                "title":"fa-glide-g",
                "searchTerms":[
                    "fa glide g"
                ]
            },
            {
                "title":"fa-globe",
                "searchTerms":[
                    "fa globe"
                ]
            },
            {
                "title":"fa-globe-africa",
                "searchTerms":[
                    "fa globe africa"
                ]
            },
            {
                "title":"fa-globe-americas",
                "searchTerms":[
                    "fa globe americas"
                ]
            },
            {
                "title":"fa-globe-asia",
                "searchTerms":[
                    "fa globe asia"
                ]
            },
            {
                "title":"fa-globe-europe",
                "searchTerms":[
                    "fa globe europe"
                ]
            },
            {
                "title":"fa-globe-snow",
                "searchTerms":[
                    "fa globe snow"
                ]
            },
            {
                "title":"fa-globe-stand",
                "searchTerms":[
                    "fa globe stand"
                ]
            },
            {
                "title":"fa-gofore",
                "searchTerms":[
                    "fa gofore"
                ]
            },
            {
                "title":"fa-golf-ball",
                "searchTerms":[
                    "fa golf ball"
                ]
            },
            {
                "title":"fa-golf-club",
                "searchTerms":[
                    "fa golf club"
                ]
            },
            {
                "title":"fa-goodreads",
                "searchTerms":[
                    "fa goodreads"
                ]
            },
            {
                "title":"fa-goodreads-g",
                "searchTerms":[
                    "fa goodreads g"
                ]
            },
            {
                "title":"fa-google",
                "searchTerms":[
                    "fa google"
                ]
            },
            {
                "title":"fa-google-drive",
                "searchTerms":[
                    "fa google drive"
                ]
            },
            {
                "title":"fa-google-play",
                "searchTerms":[
                    "fa google play"
                ]
            },
            {
                "title":"fa-google-plus",
                "searchTerms":[
                    "fa google plus"
                ]
            },
            {
                "title":"fa-google-plus-g",
                "searchTerms":[
                    "fa google plus g"
                ]
            },
            {
                "title":"fa-google-plus-square",
                "searchTerms":[
                    "fa google plus square"
                ]
            },
            {
                "title":"fa-google-wallet",
                "searchTerms":[
                    "fa google wallet"
                ]
            },
            {
                "title":"fa-gopuram",
                "searchTerms":[
                    "fa gopuram"
                ]
            },
            {
                "title":"fa-graduation-cap",
                "searchTerms":[
                    "fa graduation cap"
                ]
            },
            {
                "title":"fa-gramophone",
                "searchTerms":[
                    "fa gramophone"
                ]
            },
            {
                "title":"fa-gratipay",
                "searchTerms":[
                    "fa gratipay"
                ]
            },
            {
                "title":"fa-grav",
                "searchTerms":[
                    "fa grav"
                ]
            },
            {
                "title":"fa-greater-than",
                "searchTerms":[
                    "fa greater than"
                ]
            },
            {
                "title":"fa-greater-than-equal",
                "searchTerms":[
                    "fa greater than equal"
                ]
            },
            {
                "title":"fa-grimace",
                "searchTerms":[
                    "fa grimace"
                ]
            },
            {
                "title":"fa-grin",
                "searchTerms":[
                    "fa grin"
                ]
            },
            {
                "title":"fa-grin-alt",
                "searchTerms":[
                    "fa grin alt"
                ]
            },
            {
                "title":"fa-grin-beam",
                "searchTerms":[
                    "fa grin beam"
                ]
            },
            {
                "title":"fa-grin-beam-sweat",
                "searchTerms":[
                    "fa grin beam sweat"
                ]
            },
            {
                "title":"fa-grin-hearts",
                "searchTerms":[
                    "fa grin hearts"
                ]
            },
            {
                "title":"fa-grin-squint",
                "searchTerms":[
                    "fa grin squint"
                ]
            },
            {
                "title":"fa-grin-squint-tears",
                "searchTerms":[
                    "fa grin squint tears"
                ]
            },
            {
                "title":"fa-grin-stars",
                "searchTerms":[
                    "fa grin stars"
                ]
            },
            {
                "title":"fa-grin-tears",
                "searchTerms":[
                    "fa grin tears"
                ]
            },
            {
                "title":"fa-grin-tongue",
                "searchTerms":[
                    "fa grin tongue"
                ]
            },
            {
                "title":"fa-grin-tongue-squint",
                "searchTerms":[
                    "fa grin tongue squint"
                ]
            },
            {
                "title":"fa-grin-tongue-wink",
                "searchTerms":[
                    "fa grin tongue wink"
                ]
            },
            {
                "title":"fa-grin-wink",
                "searchTerms":[
                    "fa grin wink"
                ]
            },
            {
                "title":"fa-grip-horizontal",
                "searchTerms":[
                    "fa grip horizontal"
                ]
            },
            {
                "title":"fa-grip-lines",
                "searchTerms":[
                    "fa grip lines"
                ]
            },
            {
                "title":"fa-grip-lines-vertical",
                "searchTerms":[
                    "fa grip lines vertical"
                ]
            },
            {
                "title":"fa-grip-vertical",
                "searchTerms":[
                    "fa grip vertical"
                ]
            },
            {
                "title":"fa-gripfire",
                "searchTerms":[
                    "fa gripfire"
                ]
            },
            {
                "title":"fa-grunt",
                "searchTerms":[
                    "fa grunt"
                ]
            },
            {
                "title":"fa-guitar",
                "searchTerms":[
                    "fa guitar"
                ]
            },
            {
                "title":"fa-guitar-electric",
                "searchTerms":[
                    "fa guitar electric"
                ]
            },
            {
                "title":"fa-guitars",
                "searchTerms":[
                    "fa guitars"
                ]
            },
            {
                "title":"fa-gulp",
                "searchTerms":[
                    "fa gulp"
                ]
            },
            {
                "title":"fa-h-square",
                "searchTerms":[
                    "fa h square"
                ]
            },
            {
                "title":"fa-h1",
                "searchTerms":[
                    "fa h1"
                ]
            },
            {
                "title":"fa-h2",
                "searchTerms":[
                    "fa h2"
                ]
            },
            {
                "title":"fa-h3",
                "searchTerms":[
                    "fa h3"
                ]
            },
            {
                "title":"fa-h4",
                "searchTerms":[
                    "fa h4"
                ]
            },
            {
                "title":"fa-hacker-news",
                "searchTerms":[
                    "fa hacker news"
                ]
            },
            {
                "title":"fa-hacker-news-square",
                "searchTerms":[
                    "fa hacker news square"
                ]
            },
            {
                "title":"fa-hackerrank",
                "searchTerms":[
                    "fa hackerrank"
                ]
            },
            {
                "title":"fa-hamburger",
                "searchTerms":[
                    "fa hamburger"
                ]
            },
            {
                "title":"fa-hammer",
                "searchTerms":[
                    "fa hammer"
                ]
            },
            {
                "title":"fa-hammer-war",
                "searchTerms":[
                    "fa hammer war"
                ]
            },
            {
                "title":"fa-hamsa",
                "searchTerms":[
                    "fa hamsa"
                ]
            },
            {
                "title":"fa-hand-heart",
                "searchTerms":[
                    "fa hand heart"
                ]
            },
            {
                "title":"fa-hand-holding",
                "searchTerms":[
                    "fa hand holding"
                ]
            },
            {
                "title":"fa-hand-holding-box",
                "searchTerms":[
                    "fa hand holding box"
                ]
            },
            {
                "title":"fa-hand-holding-heart",
                "searchTerms":[
                    "fa hand holding heart"
                ]
            },
            {
                "title":"fa-hand-holding-magic",
                "searchTerms":[
                    "fa hand holding magic"
                ]
            },
            {
                "title":"fa-hand-holding-seedling",
                "searchTerms":[
                    "fa hand holding seedling"
                ]
            },
            {
                "title":"fa-hand-holding-usd",
                "searchTerms":[
                    "fa hand holding usd"
                ]
            },
            {
                "title":"fa-hand-holding-water",
                "searchTerms":[
                    "fa hand holding water"
                ]
            },
            {
                "title":"fa-hand-lizard",
                "searchTerms":[
                    "fa hand lizard"
                ]
            },
            {
                "title":"fa-hand-middle-finger",
                "searchTerms":[
                    "fa hand middle finger"
                ]
            },
            {
                "title":"fa-hand-paper",
                "searchTerms":[
                    "fa hand paper"
                ]
            },
            {
                "title":"fa-hand-peace",
                "searchTerms":[
                    "fa hand peace"
                ]
            },
            {
                "title":"fa-hand-point-down",
                "searchTerms":[
                    "fa hand point down"
                ]
            },
            {
                "title":"fa-hand-point-left",
                "searchTerms":[
                    "fa hand point left"
                ]
            },
            {
                "title":"fa-hand-point-right",
                "searchTerms":[
                    "fa hand point right"
                ]
            },
            {
                "title":"fa-hand-point-up",
                "searchTerms":[
                    "fa hand point up"
                ]
            },
            {
                "title":"fa-hand-pointer",
                "searchTerms":[
                    "fa hand pointer"
                ]
            },
            {
                "title":"fa-hand-receiving",
                "searchTerms":[
                    "fa hand receiving"
                ]
            },
            {
                "title":"fa-hand-rock",
                "searchTerms":[
                    "fa hand rock"
                ]
            },
            {
                "title":"fa-hand-scissors",
                "searchTerms":[
                    "fa hand scissors"
                ]
            },
            {
                "title":"fa-hand-spock",
                "searchTerms":[
                    "fa hand spock"
                ]
            },
            {
                "title":"fa-hands",
                "searchTerms":[
                    "fa hands"
                ]
            },
            {
                "title":"fa-hands-heart",
                "searchTerms":[
                    "fa hands heart"
                ]
            },
            {
                "title":"fa-hands-helping",
                "searchTerms":[
                    "fa hands helping"
                ]
            },
            {
                "title":"fa-hands-usd",
                "searchTerms":[
                    "fa hands usd"
                ]
            },
            {
                "title":"fa-handshake",
                "searchTerms":[
                    "fa handshake"
                ]
            },
            {
                "title":"fa-handshake-alt",
                "searchTerms":[
                    "fa handshake alt"
                ]
            },
            {
                "title":"fa-hanukiah",
                "searchTerms":[
                    "fa hanukiah"
                ]
            },
            {
                "title":"fa-hard-hat",
                "searchTerms":[
                    "fa hard hat"
                ]
            },
            {
                "title":"fa-hashtag",
                "searchTerms":[
                    "fa hashtag"
                ]
            },
            {
                "title":"fa-hat-chef",
                "searchTerms":[
                    "fa hat chef"
                ]
            },
            {
                "title":"fa-hat-cowboy",
                "searchTerms":[
                    "fa hat cowboy"
                ]
            },
            {
                "title":"fa-hat-cowboy-side",
                "searchTerms":[
                    "fa hat cowboy side"
                ]
            },
            {
                "title":"fa-hat-santa",
                "searchTerms":[
                    "fa hat santa"
                ]
            },
            {
                "title":"fa-hat-winter",
                "searchTerms":[
                    "fa hat winter"
                ]
            },
            {
                "title":"fa-hat-witch",
                "searchTerms":[
                    "fa hat witch"
                ]
            },
            {
                "title":"fa-hat-wizard",
                "searchTerms":[
                    "fa hat wizard"
                ]
            },
            {
                "title":"fa-haykal",
                "searchTerms":[
                    "fa haykal"
                ]
            },
            {
                "title":"fa-hdd",
                "searchTerms":[
                    "fa hdd"
                ]
            },
            {
                "title":"fa-head-side",
                "searchTerms":[
                    "fa head side"
                ]
            },
            {
                "title":"fa-head-side-brain",
                "searchTerms":[
                    "fa head side brain"
                ]
            },
            {
                "title":"fa-head-side-headphones",
                "searchTerms":[
                    "fa head side headphones"
                ]
            },
            {
                "title":"fa-head-side-medical",
                "searchTerms":[
                    "fa head side medical"
                ]
            },
            {
                "title":"fa-head-vr",
                "searchTerms":[
                    "fa head vr"
                ]
            },
            {
                "title":"fa-heading",
                "searchTerms":[
                    "fa heading"
                ]
            },
            {
                "title":"fa-headphones",
                "searchTerms":[
                    "fa headphones"
                ]
            },
            {
                "title":"fa-headphones-alt",
                "searchTerms":[
                    "fa headphones alt"
                ]
            },
            {
                "title":"fa-headset",
                "searchTerms":[
                    "fa headset"
                ]
            },
            {
                "title":"fa-heart",
                "searchTerms":[
                    "fa heart"
                ]
            },
            {
                "title":"fa-heart-broken",
                "searchTerms":[
                    "fa heart broken"
                ]
            },
            {
                "title":"fa-heart-circle",
                "searchTerms":[
                    "fa heart circle"
                ]
            },
            {
                "title":"fa-heart-rate",
                "searchTerms":[
                    "fa heart rate"
                ]
            },
            {
                "title":"fa-heart-square",
                "searchTerms":[
                    "fa heart square"
                ]
            },
            {
                "title":"fa-heartbeat",
                "searchTerms":[
                    "fa heartbeat"
                ]
            },
            {
                "title":"fa-helicopter",
                "searchTerms":[
                    "fa helicopter"
                ]
            },
            {
                "title":"fa-helmet-battle",
                "searchTerms":[
                    "fa helmet battle"
                ]
            },
            {
                "title":"fa-hexagon",
                "searchTerms":[
                    "fa hexagon"
                ]
            },
            {
                "title":"fa-highlighter",
                "searchTerms":[
                    "fa highlighter"
                ]
            },
            {
                "title":"fa-hiking",
                "searchTerms":[
                    "fa hiking"
                ]
            },
            {
                "title":"fa-hippo",
                "searchTerms":[
                    "fa hippo"
                ]
            },
            {
                "title":"fa-hips",
                "searchTerms":[
                    "fa hips"
                ]
            },
            {
                "title":"fa-hire-a-helper",
                "searchTerms":[
                    "fa hire a helper"
                ]
            },
            {
                "title":"fa-history",
                "searchTerms":[
                    "fa history"
                ]
            },
            {
                "title":"fa-hockey-mask",
                "searchTerms":[
                    "fa hockey mask"
                ]
            },
            {
                "title":"fa-hockey-puck",
                "searchTerms":[
                    "fa hockey puck"
                ]
            },
            {
                "title":"fa-hockey-sticks",
                "searchTerms":[
                    "fa hockey sticks"
                ]
            },
            {
                "title":"fa-holly-berry",
                "searchTerms":[
                    "fa holly berry"
                ]
            },
            {
                "title":"fa-home",
                "searchTerms":[
                    "fa home"
                ]
            },
            {
                "title":"fa-home-alt",
                "searchTerms":[
                    "fa home alt"
                ]
            },
            {
                "title":"fa-home-heart",
                "searchTerms":[
                    "fa home heart"
                ]
            },
            {
                "title":"fa-home-lg",
                "searchTerms":[
                    "fa home lg"
                ]
            },
            {
                "title":"fa-home-lg-alt",
                "searchTerms":[
                    "fa home lg alt"
                ]
            },
            {
                "title":"fa-hood-cloak",
                "searchTerms":[
                    "fa hood cloak"
                ]
            },
            {
                "title":"fa-hooli",
                "searchTerms":[
                    "fa hooli"
                ]
            },
            {
                "title":"fa-horizontal-rule",
                "searchTerms":[
                    "fa horizontal rule"
                ]
            },
            {
                "title":"fa-hornbill",
                "searchTerms":[
                    "fa hornbill"
                ]
            },
            {
                "title":"fa-horse",
                "searchTerms":[
                    "fa horse"
                ]
            },
            {
                "title":"fa-horse-head",
                "searchTerms":[
                    "fa horse head"
                ]
            },
            {
                "title":"fa-horse-saddle",
                "searchTerms":[
                    "fa horse saddle"
                ]
            },
            {
                "title":"fa-hospital",
                "searchTerms":[
                    "fa hospital"
                ]
            },
            {
                "title":"fa-hospital-alt",
                "searchTerms":[
                    "fa hospital alt"
                ]
            },
            {
                "title":"fa-hospital-symbol",
                "searchTerms":[
                    "fa hospital symbol"
                ]
            },
            {
                "title":"fa-hospital-user",
                "searchTerms":[
                    "fa hospital user"
                ]
            },
            {
                "title":"fa-hospitals",
                "searchTerms":[
                    "fa hospitals"
                ]
            },
            {
                "title":"fa-hot-tub",
                "searchTerms":[
                    "fa hot tub"
                ]
            },
            {
                "title":"fa-hotdog",
                "searchTerms":[
                    "fa hotdog"
                ]
            },
            {
                "title":"fa-hotel",
                "searchTerms":[
                    "fa hotel"
                ]
            },
            {
                "title":"fa-hotjar",
                "searchTerms":[
                    "fa hotjar"
                ]
            },
            {
                "title":"fa-hourglass",
                "searchTerms":[
                    "fa hourglass"
                ]
            },
            {
                "title":"fa-hourglass-end",
                "searchTerms":[
                    "fa hourglass end"
                ]
            },
            {
                "title":"fa-hourglass-half",
                "searchTerms":[
                    "fa hourglass half"
                ]
            },
            {
                "title":"fa-hourglass-start",
                "searchTerms":[
                    "fa hourglass start"
                ]
            },
            {
                "title":"fa-house-damage",
                "searchTerms":[
                    "fa house damage"
                ]
            },
            {
                "title":"fa-house-flood",
                "searchTerms":[
                    "fa house flood"
                ]
            },
            {
                "title":"fa-houzz",
                "searchTerms":[
                    "fa houzz"
                ]
            },
            {
                "title":"fa-hryvnia",
                "searchTerms":[
                    "fa hryvnia"
                ]
            },
            {
                "title":"fa-html5",
                "searchTerms":[
                    "fa html5"
                ]
            },
            {
                "title":"fa-hubspot",
                "searchTerms":[
                    "fa hubspot"
                ]
            },
            {
                "title":"fa-humidity",
                "searchTerms":[
                    "fa humidity"
                ]
            },
            {
                "title":"fa-hurricane",
                "searchTerms":[
                    "fa hurricane"
                ]
            },
            {
                "title":"fa-i-cursor",
                "searchTerms":[
                    "fa i cursor"
                ]
            },
            {
                "title":"fa-ice-cream",
                "searchTerms":[
                    "fa ice cream"
                ]
            },
            {
                "title":"fa-ice-skate",
                "searchTerms":[
                    "fa ice skate"
                ]
            },
            {
                "title":"fa-icicles",
                "searchTerms":[
                    "fa icicles"
                ]
            },
            {
                "title":"fa-icons",
                "searchTerms":[
                    "fa icons"
                ]
            },
            {
                "title":"fa-icons-alt",
                "searchTerms":[
                    "fa icons alt"
                ]
            },
            {
                "title":"fa-id-badge",
                "searchTerms":[
                    "fa id badge"
                ]
            },
            {
                "title":"fa-id-card",
                "searchTerms":[
                    "fa id card"
                ]
            },
            {
                "title":"fa-id-card-alt",
                "searchTerms":[
                    "fa id card alt"
                ]
            },
            {
                "title":"fa-igloo",
                "searchTerms":[
                    "fa igloo"
                ]
            },
            {
                "title":"fa-image",
                "searchTerms":[
                    "fa image"
                ]
            },
            {
                "title":"fa-image-polaroid",
                "searchTerms":[
                    "fa image polaroid"
                ]
            },
            {
                "title":"fa-images",
                "searchTerms":[
                    "fa images"
                ]
            },
            {
                "title":"fa-imdb",
                "searchTerms":[
                    "fa imdb"
                ]
            },
            {
                "title":"fa-inbox",
                "searchTerms":[
                    "fa inbox"
                ]
            },
            {
                "title":"fa-inbox-in",
                "searchTerms":[
                    "fa inbox in"
                ]
            },
            {
                "title":"fa-inbox-out",
                "searchTerms":[
                    "fa inbox out"
                ]
            },
            {
                "title":"fa-indent",
                "searchTerms":[
                    "fa indent"
                ]
            },
            {
                "title":"fa-industry",
                "searchTerms":[
                    "fa industry"
                ]
            },
            {
                "title":"fa-industry-alt",
                "searchTerms":[
                    "fa industry alt"
                ]
            },
            {
                "title":"fa-infinity",
                "searchTerms":[
                    "fa infinity"
                ]
            },
            {
                "title":"fa-info",
                "searchTerms":[
                    "fa info"
                ]
            },
            {
                "title":"fa-info-circle",
                "searchTerms":[
                    "fa info circle"
                ]
            },
            {
                "title":"fa-info-square",
                "searchTerms":[
                    "fa info square"
                ]
            },
            {
                "title":"fa-inhaler",
                "searchTerms":[
                    "fa inhaler"
                ]
            },
            {
                "title":"fa-instagram",
                "searchTerms":[
                    "fa instagram"
                ]
            },
            {
                "title":"fa-integral",
                "searchTerms":[
                    "fa integral"
                ]
            },
            {
                "title":"fa-intercom",
                "searchTerms":[
                    "fa intercom"
                ]
            },
            {
                "title":"fa-internet-explorer",
                "searchTerms":[
                    "fa internet explorer"
                ]
            },
            {
                "title":"fa-intersection",
                "searchTerms":[
                    "fa intersection"
                ]
            },
            {
                "title":"fa-inventory",
                "searchTerms":[
                    "fa inventory"
                ]
            },
            {
                "title":"fa-invision",
                "searchTerms":[
                    "fa invision"
                ]
            },
            {
                "title":"fa-ioxhost",
                "searchTerms":[
                    "fa ioxhost"
                ]
            },
            {
                "title":"fa-island-tropical",
                "searchTerms":[
                    "fa island tropical"
                ]
            },
            {
                "title":"fa-italic",
                "searchTerms":[
                    "fa italic"
                ]
            },
            {
                "title":"fa-itch-io",
                "searchTerms":[
                    "fa itch io"
                ]
            },
            {
                "title":"fa-itunes",
                "searchTerms":[
                    "fa itunes"
                ]
            },
            {
                "title":"fa-itunes-note",
                "searchTerms":[
                    "fa itunes note"
                ]
            },
            {
                "title":"fa-jack-o-lantern",
                "searchTerms":[
                    "fa jack o lantern"
                ]
            },
            {
                "title":"fa-java",
                "searchTerms":[
                    "fa java"
                ]
            },
            {
                "title":"fa-jedi",
                "searchTerms":[
                    "fa jedi"
                ]
            },
            {
                "title":"fa-jedi-order",
                "searchTerms":[
                    "fa jedi order"
                ]
            },
            {
                "title":"fa-jenkins",
                "searchTerms":[
                    "fa jenkins"
                ]
            },
            {
                "title":"fa-jira",
                "searchTerms":[
                    "fa jira"
                ]
            },
            {
                "title":"fa-joget",
                "searchTerms":[
                    "fa joget"
                ]
            },
            {
                "title":"fa-joint",
                "searchTerms":[
                    "fa joint"
                ]
            },
            {
                "title":"fa-joomla",
                "searchTerms":[
                    "fa joomla"
                ]
            },
            {
                "title":"fa-journal-whills",
                "searchTerms":[
                    "fa journal whills"
                ]
            },
            {
                "title":"fa-joystick",
                "searchTerms":[
                    "fa joystick"
                ]
            },
            {
                "title":"fa-js",
                "searchTerms":[
                    "fa js"
                ]
            },
            {
                "title":"fa-js-square",
                "searchTerms":[
                    "fa js square"
                ]
            },
            {
                "title":"fa-jsfiddle",
                "searchTerms":[
                    "fa jsfiddle"
                ]
            },
            {
                "title":"fa-jug",
                "searchTerms":[
                    "fa jug"
                ]
            },
            {
                "title":"fa-kaaba",
                "searchTerms":[
                    "fa kaaba"
                ]
            },
            {
                "title":"fa-kaggle",
                "searchTerms":[
                    "fa kaggle"
                ]
            },
            {
                "title":"fa-kazoo",
                "searchTerms":[
                    "fa kazoo"
                ]
            },
            {
                "title":"fa-kerning",
                "searchTerms":[
                    "fa kerning"
                ]
            },
            {
                "title":"fa-key",
                "searchTerms":[
                    "fa key"
                ]
            },
            {
                "title":"fa-key-skeleton",
                "searchTerms":[
                    "fa key skeleton"
                ]
            },
            {
                "title":"fa-keybase",
                "searchTerms":[
                    "fa keybase"
                ]
            },
            {
                "title":"fa-keyboard",
                "searchTerms":[
                    "fa keyboard"
                ]
            },
            {
                "title":"fa-keycdn",
                "searchTerms":[
                    "fa keycdn"
                ]
            },
            {
                "title":"fa-keynote",
                "searchTerms":[
                    "fa keynote"
                ]
            },
            {
                "title":"fa-khanda",
                "searchTerms":[
                    "fa khanda"
                ]
            },
            {
                "title":"fa-kickstarter",
                "searchTerms":[
                    "fa kickstarter"
                ]
            },
            {
                "title":"fa-kickstarter-k",
                "searchTerms":[
                    "fa kickstarter k"
                ]
            },
            {
                "title":"fa-kidneys",
                "searchTerms":[
                    "fa kidneys"
                ]
            },
            {
                "title":"fa-kiss",
                "searchTerms":[
                    "fa kiss"
                ]
            },
            {
                "title":"fa-kiss-beam",
                "searchTerms":[
                    "fa kiss beam"
                ]
            },
            {
                "title":"fa-kiss-wink-heart",
                "searchTerms":[
                    "fa kiss wink heart"
                ]
            },
            {
                "title":"fa-kite",
                "searchTerms":[
                    "fa kite"
                ]
            },
            {
                "title":"fa-kiwi-bird",
                "searchTerms":[
                    "fa kiwi bird"
                ]
            },
            {
                "title":"fa-knife-kitchen",
                "searchTerms":[
                    "fa knife kitchen"
                ]
            },
            {
                "title":"fa-korvue",
                "searchTerms":[
                    "fa korvue"
                ]
            },
            {
                "title":"fa-lambda",
                "searchTerms":[
                    "fa lambda"
                ]
            },
            {
                "title":"fa-lamp",
                "searchTerms":[
                    "fa lamp"
                ]
            },
            {
                "title":"fa-landmark",
                "searchTerms":[
                    "fa landmark"
                ]
            },
            {
                "title":"fa-landmark-alt",
                "searchTerms":[
                    "fa landmark alt"
                ]
            },
            {
                "title":"fa-language",
                "searchTerms":[
                    "fa language"
                ]
            },
            {
                "title":"fa-laptop",
                "searchTerms":[
                    "fa laptop"
                ]
            },
            {
                "title":"fa-laptop-code",
                "searchTerms":[
                    "fa laptop code"
                ]
            },
            {
                "title":"fa-laptop-medical",
                "searchTerms":[
                    "fa laptop medical"
                ]
            },
            {
                "title":"fa-laravel",
                "searchTerms":[
                    "fa laravel"
                ]
            },
            {
                "title":"fa-lasso",
                "searchTerms":[
                    "fa lasso"
                ]
            },
            {
                "title":"fa-lastfm",
                "searchTerms":[
                    "fa lastfm"
                ]
            },
            {
                "title":"fa-lastfm-square",
                "searchTerms":[
                    "fa lastfm square"
                ]
            },
            {
                "title":"fa-laugh",
                "searchTerms":[
                    "fa laugh"
                ]
            },
            {
                "title":"fa-laugh-beam",
                "searchTerms":[
                    "fa laugh beam"
                ]
            },
            {
                "title":"fa-laugh-squint",
                "searchTerms":[
                    "fa laugh squint"
                ]
            },
            {
                "title":"fa-laugh-wink",
                "searchTerms":[
                    "fa laugh wink"
                ]
            },
            {
                "title":"fa-layer-group",
                "searchTerms":[
                    "fa layer group"
                ]
            },
            {
                "title":"fa-layer-minus",
                "searchTerms":[
                    "fa layer minus"
                ]
            },
            {
                "title":"fa-layer-plus",
                "searchTerms":[
                    "fa layer plus"
                ]
            },
            {
                "title":"fa-leaf",
                "searchTerms":[
                    "fa leaf"
                ]
            },
            {
                "title":"fa-leaf-heart",
                "searchTerms":[
                    "fa leaf heart"
                ]
            },
            {
                "title":"fa-leaf-maple",
                "searchTerms":[
                    "fa leaf maple"
                ]
            },
            {
                "title":"fa-leaf-oak",
                "searchTerms":[
                    "fa leaf oak"
                ]
            },
            {
                "title":"fa-leanpub",
                "searchTerms":[
                    "fa leanpub"
                ]
            },
            {
                "title":"fa-lemon",
                "searchTerms":[
                    "fa lemon"
                ]
            },
            {
                "title":"fa-less",
                "searchTerms":[
                    "fa less"
                ]
            },
            {
                "title":"fa-less-than",
                "searchTerms":[
                    "fa less than"
                ]
            },
            {
                "title":"fa-less-than-equal",
                "searchTerms":[
                    "fa less than equal"
                ]
            },
            {
                "title":"fa-level-down",
                "searchTerms":[
                    "fa level down"
                ]
            },
            {
                "title":"fa-level-down-alt",
                "searchTerms":[
                    "fa level down alt"
                ]
            },
            {
                "title":"fa-level-up",
                "searchTerms":[
                    "fa level up"
                ]
            },
            {
                "title":"fa-level-up-alt",
                "searchTerms":[
                    "fa level up alt"
                ]
            },
            {
                "title":"fa-life-ring",
                "searchTerms":[
                    "fa life ring"
                ]
            },
            {
                "title":"fa-lightbulb",
                "searchTerms":[
                    "fa lightbulb"
                ]
            },
            {
                "title":"fa-lightbulb-dollar",
                "searchTerms":[
                    "fa lightbulb dollar"
                ]
            },
            {
                "title":"fa-lightbulb-exclamation",
                "searchTerms":[
                    "fa lightbulb exclamation"
                ]
            },
            {
                "title":"fa-lightbulb-on",
                "searchTerms":[
                    "fa lightbulb on"
                ]
            },
            {
                "title":"fa-lightbulb-slash",
                "searchTerms":[
                    "fa lightbulb slash"
                ]
            },
            {
                "title":"fa-lights-holiday",
                "searchTerms":[
                    "fa lights holiday"
                ]
            },
            {
                "title":"fa-line",
                "searchTerms":[
                    "fa line"
                ]
            },
            {
                "title":"fa-line-columns",
                "searchTerms":[
                    "fa line columns"
                ]
            },
            {
                "title":"fa-line-height",
                "searchTerms":[
                    "fa line height"
                ]
            },
            {
                "title":"fa-link",
                "searchTerms":[
                    "fa link"
                ]
            },
            {
                "title":"fa-linkedin",
                "searchTerms":[
                    "fa linkedin"
                ]
            },
            {
                "title":"fa-linkedin-in",
                "searchTerms":[
                    "fa linkedin in"
                ]
            },
            {
                "title":"fa-linode",
                "searchTerms":[
                    "fa linode"
                ]
            },
            {
                "title":"fa-linux",
                "searchTerms":[
                    "fa linux"
                ]
            },
            {
                "title":"fa-lips",
                "searchTerms":[
                    "fa lips"
                ]
            },
            {
                "title":"fa-lira-sign",
                "searchTerms":[
                    "fa lira sign"
                ]
            },
            {
                "title":"fa-list",
                "searchTerms":[
                    "fa list"
                ]
            },
            {
                "title":"fa-list-alt",
                "searchTerms":[
                    "fa list alt"
                ]
            },
            {
                "title":"fa-list-music",
                "searchTerms":[
                    "fa list music"
                ]
            },
            {
                "title":"fa-list-ol",
                "searchTerms":[
                    "fa list ol"
                ]
            },
            {
                "title":"fa-list-ul",
                "searchTerms":[
                    "fa list ul"
                ]
            },
            {
                "title":"fa-location",
                "searchTerms":[
                    "fa location"
                ]
            },
            {
                "title":"fa-location-arrow",
                "searchTerms":[
                    "fa location arrow"
                ]
            },
            {
                "title":"fa-location-circle",
                "searchTerms":[
                    "fa location circle"
                ]
            },
            {
                "title":"fa-location-slash",
                "searchTerms":[
                    "fa location slash"
                ]
            },
            {
                "title":"fa-lock",
                "searchTerms":[
                    "fa lock"
                ]
            },
            {
                "title":"fa-lock-alt",
                "searchTerms":[
                    "fa lock alt"
                ]
            },
            {
                "title":"fa-lock-open",
                "searchTerms":[
                    "fa lock open"
                ]
            },
            {
                "title":"fa-lock-open-alt",
                "searchTerms":[
                    "fa lock open alt"
                ]
            },
            {
                "title":"fa-long-arrow-alt-down",
                "searchTerms":[
                    "fa long arrow alt down"
                ]
            },
            {
                "title":"fa-long-arrow-alt-left",
                "searchTerms":[
                    "fa long arrow alt left"
                ]
            },
            {
                "title":"fa-long-arrow-alt-right",
                "searchTerms":[
                    "fa long arrow alt right"
                ]
            },
            {
                "title":"fa-long-arrow-alt-up",
                "searchTerms":[
                    "fa long arrow alt up"
                ]
            },
            {
                "title":"fa-long-arrow-down",
                "searchTerms":[
                    "fa long arrow down"
                ]
            },
            {
                "title":"fa-long-arrow-left",
                "searchTerms":[
                    "fa long arrow left"
                ]
            },
            {
                "title":"fa-long-arrow-right",
                "searchTerms":[
                    "fa long arrow right"
                ]
            },
            {
                "title":"fa-long-arrow-up",
                "searchTerms":[
                    "fa long arrow up"
                ]
            },
            {
                "title":"fa-loveseat",
                "searchTerms":[
                    "fa loveseat"
                ]
            },
            {
                "title":"fa-low-vision",
                "searchTerms":[
                    "fa low vision"
                ]
            },
            {
                "title":"fa-luchador",
                "searchTerms":[
                    "fa luchador"
                ]
            },
            {
                "title":"fa-luggage-cart",
                "searchTerms":[
                    "fa luggage cart"
                ]
            },
            {
                "title":"fa-lungs",
                "searchTerms":[
                    "fa lungs"
                ]
            },
            {
                "title":"fa-lyft",
                "searchTerms":[
                    "fa lyft"
                ]
            },
            {
                "title":"fa-mace",
                "searchTerms":[
                    "fa mace"
                ]
            },
            {
                "title":"fa-magento",
                "searchTerms":[
                    "fa magento"
                ]
            },
            {
                "title":"fa-magic",
                "searchTerms":[
                    "fa magic"
                ]
            },
            {
                "title":"fa-magnet",
                "searchTerms":[
                    "fa magnet"
                ]
            },
            {
                "title":"fa-mail-bulk",
                "searchTerms":[
                    "fa mail bulk"
                ]
            },
            {
                "title":"fa-mailbox",
                "searchTerms":[
                    "fa mailbox"
                ]
            },
            {
                "title":"fa-mailchimp",
                "searchTerms":[
                    "fa mailchimp"
                ]
            },
            {
                "title":"fa-male",
                "searchTerms":[
                    "fa male"
                ]
            },
            {
                "title":"fa-mandalorian",
                "searchTerms":[
                    "fa mandalorian"
                ]
            },
            {
                "title":"fa-mandolin",
                "searchTerms":[
                    "fa mandolin"
                ]
            },
            {
                "title":"fa-map",
                "searchTerms":[
                    "fa map"
                ]
            },
            {
                "title":"fa-map-marked",
                "searchTerms":[
                    "fa map marked"
                ]
            },
            {
                "title":"fa-map-marked-alt",
                "searchTerms":[
                    "fa map marked alt"
                ]
            },
            {
                "title":"fa-map-marker",
                "searchTerms":[
                    "fa map marker"
                ]
            },
            {
                "title":"fa-map-marker-alt",
                "searchTerms":[
                    "fa map marker alt"
                ]
            },
            {
                "title":"fa-map-marker-alt-slash",
                "searchTerms":[
                    "fa map marker alt slash"
                ]
            },
            {
                "title":"fa-map-marker-check",
                "searchTerms":[
                    "fa map marker check"
                ]
            },
            {
                "title":"fa-map-marker-edit",
                "searchTerms":[
                    "fa map marker edit"
                ]
            },
            {
                "title":"fa-map-marker-exclamation",
                "searchTerms":[
                    "fa map marker exclamation"
                ]
            },
            {
                "title":"fa-map-marker-minus",
                "searchTerms":[
                    "fa map marker minus"
                ]
            },
            {
                "title":"fa-map-marker-plus",
                "searchTerms":[
                    "fa map marker plus"
                ]
            },
            {
                "title":"fa-map-marker-question",
                "searchTerms":[
                    "fa map marker question"
                ]
            },
            {
                "title":"fa-map-marker-slash",
                "searchTerms":[
                    "fa map marker slash"
                ]
            },
            {
                "title":"fa-map-marker-smile",
                "searchTerms":[
                    "fa map marker smile"
                ]
            },
            {
                "title":"fa-map-marker-times",
                "searchTerms":[
                    "fa map marker times"
                ]
            },
            {
                "title":"fa-map-pin",
                "searchTerms":[
                    "fa map pin"
                ]
            },
            {
                "title":"fa-map-signs",
                "searchTerms":[
                    "fa map signs"
                ]
            },
            {
                "title":"fa-markdown",
                "searchTerms":[
                    "fa markdown"
                ]
            },
            {
                "title":"fa-marker",
                "searchTerms":[
                    "fa marker"
                ]
            },
            {
                "title":"fa-mars",
                "searchTerms":[
                    "fa mars"
                ]
            },
            {
                "title":"fa-mars-double",
                "searchTerms":[
                    "fa mars double"
                ]
            },
            {
                "title":"fa-mars-stroke",
                "searchTerms":[
                    "fa mars stroke"
                ]
            },
            {
                "title":"fa-mars-stroke-h",
                "searchTerms":[
                    "fa mars stroke h"
                ]
            },
            {
                "title":"fa-mars-stroke-v",
                "searchTerms":[
                    "fa mars stroke v"
                ]
            },
            {
                "title":"fa-mask",
                "searchTerms":[
                    "fa mask"
                ]
            },
            {
                "title":"fa-mastodon",
                "searchTerms":[
                    "fa mastodon"
                ]
            },
            {
                "title":"fa-maxcdn",
                "searchTerms":[
                    "fa maxcdn"
                ]
            },
            {
                "title":"fa-mdb",
                "searchTerms":[
                    "fa mdb"
                ]
            },
            {
                "title":"fa-meat",
                "searchTerms":[
                    "fa meat"
                ]
            },
            {
                "title":"fa-medal",
                "searchTerms":[
                    "fa medal"
                ]
            },
            {
                "title":"fa-medapps",
                "searchTerms":[
                    "fa medapps"
                ]
            },
            {
                "title":"fa-medium",
                "searchTerms":[
                    "fa medium"
                ]
            },
            {
                "title":"fa-medium-m",
                "searchTerms":[
                    "fa medium m"
                ]
            },
            {
                "title":"fa-medkit",
                "searchTerms":[
                    "fa medkit"
                ]
            },
            {
                "title":"fa-medrt",
                "searchTerms":[
                    "fa medrt"
                ]
            },
            {
                "title":"fa-meetup",
                "searchTerms":[
                    "fa meetup"
                ]
            },
            {
                "title":"fa-megaphone",
                "searchTerms":[
                    "fa megaphone"
                ]
            },
            {
                "title":"fa-megaport",
                "searchTerms":[
                    "fa megaport"
                ]
            },
            {
                "title":"fa-meh",
                "searchTerms":[
                    "fa meh"
                ]
            },
            {
                "title":"fa-meh-blank",
                "searchTerms":[
                    "fa meh blank"
                ]
            },
            {
                "title":"fa-meh-rolling-eyes",
                "searchTerms":[
                    "fa meh rolling eyes"
                ]
            },
            {
                "title":"fa-memory",
                "searchTerms":[
                    "fa memory"
                ]
            },
            {
                "title":"fa-mendeley",
                "searchTerms":[
                    "fa mendeley"
                ]
            },
            {
                "title":"fa-menorah",
                "searchTerms":[
                    "fa menorah"
                ]
            },
            {
                "title":"fa-mercury",
                "searchTerms":[
                    "fa mercury"
                ]
            },
            {
                "title":"fa-meteor",
                "searchTerms":[
                    "fa meteor"
                ]
            },
            {
                "title":"fa-microchip",
                "searchTerms":[
                    "fa microchip"
                ]
            },
            {
                "title":"fa-microphone",
                "searchTerms":[
                    "fa microphone"
                ]
            },
            {
                "title":"fa-microphone-alt",
                "searchTerms":[
                    "fa microphone alt"
                ]
            },
            {
                "title":"fa-microphone-alt-slash",
                "searchTerms":[
                    "fa microphone alt slash"
                ]
            },
            {
                "title":"fa-microphone-slash",
                "searchTerms":[
                    "fa microphone slash"
                ]
            },
            {
                "title":"fa-microphone-stand",
                "searchTerms":[
                    "fa microphone stand"
                ]
            },
            {
                "title":"fa-microscope",
                "searchTerms":[
                    "fa microscope"
                ]
            },
            {
                "title":"fa-microsoft",
                "searchTerms":[
                    "fa microsoft"
                ]
            },
            {
                "title":"fa-mind-share",
                "searchTerms":[
                    "fa mind share"
                ]
            },
            {
                "title":"fa-minus",
                "searchTerms":[
                    "fa minus"
                ]
            },
            {
                "title":"fa-minus-circle",
                "searchTerms":[
                    "fa minus circle"
                ]
            },
            {
                "title":"fa-minus-hexagon",
                "searchTerms":[
                    "fa minus hexagon"
                ]
            },
            {
                "title":"fa-minus-octagon",
                "searchTerms":[
                    "fa minus octagon"
                ]
            },
            {
                "title":"fa-minus-square",
                "searchTerms":[
                    "fa minus square"
                ]
            },
            {
                "title":"fa-mistletoe",
                "searchTerms":[
                    "fa mistletoe"
                ]
            },
            {
                "title":"fa-mitten",
                "searchTerms":[
                    "fa mitten"
                ]
            },
            {
                "title":"fa-mix",
                "searchTerms":[
                    "fa mix"
                ]
            },
            {
                "title":"fa-mixcloud",
                "searchTerms":[
                    "fa mixcloud"
                ]
            },
            {
                "title":"fa-mizuni",
                "searchTerms":[
                    "fa mizuni"
                ]
            },
            {
                "title":"fa-mobile",
                "searchTerms":[
                    "fa mobile"
                ]
            },
            {
                "title":"fa-mobile-alt",
                "searchTerms":[
                    "fa mobile alt"
                ]
            },
            {
                "title":"fa-mobile-android",
                "searchTerms":[
                    "fa mobile android"
                ]
            },
            {
                "title":"fa-mobile-android-alt",
                "searchTerms":[
                    "fa mobile android alt"
                ]
            },
            {
                "title":"fa-modx",
                "searchTerms":[
                    "fa modx"
                ]
            },
            {
                "title":"fa-monero",
                "searchTerms":[
                    "fa monero"
                ]
            },
            {
                "title":"fa-money-bill",
                "searchTerms":[
                    "fa money bill"
                ]
            },
            {
                "title":"fa-money-bill-alt",
                "searchTerms":[
                    "fa money bill alt"
                ]
            },
            {
                "title":"fa-money-bill-wave",
                "searchTerms":[
                    "fa money bill wave"
                ]
            },
            {
                "title":"fa-money-bill-wave-alt",
                "searchTerms":[
                    "fa money bill wave alt"
                ]
            },
            {
                "title":"fa-money-check",
                "searchTerms":[
                    "fa money check"
                ]
            },
            {
                "title":"fa-money-check-alt",
                "searchTerms":[
                    "fa money check alt"
                ]
            },
            {
                "title":"fa-money-check-edit",
                "searchTerms":[
                    "fa money check edit"
                ]
            },
            {
                "title":"fa-money-check-edit-alt",
                "searchTerms":[
                    "fa money check edit alt"
                ]
            },
            {
                "title":"fa-monitor-heart-rate",
                "searchTerms":[
                    "fa monitor heart rate"
                ]
            },
            {
                "title":"fa-monkey",
                "searchTerms":[
                    "fa monkey"
                ]
            },
            {
                "title":"fa-monument",
                "searchTerms":[
                    "fa monument"
                ]
            },
            {
                "title":"fa-moon",
                "searchTerms":[
                    "fa moon"
                ]
            },
            {
                "title":"fa-moon-cloud",
                "searchTerms":[
                    "fa moon cloud"
                ]
            },
            {
                "title":"fa-moon-stars",
                "searchTerms":[
                    "fa moon stars"
                ]
            },
            {
                "title":"fa-mortar-pestle",
                "searchTerms":[
                    "fa mortar pestle"
                ]
            },
            {
                "title":"fa-mosque",
                "searchTerms":[
                    "fa mosque"
                ]
            },
            {
                "title":"fa-motorcycle",
                "searchTerms":[
                    "fa motorcycle"
                ]
            },
            {
                "title":"fa-mountain",
                "searchTerms":[
                    "fa mountain"
                ]
            },
            {
                "title":"fa-mountains",
                "searchTerms":[
                    "fa mountains"
                ]
            },
            {
                "title":"fa-mouse",
                "searchTerms":[
                    "fa mouse"
                ]
            },
            {
                "title":"fa-mouse-alt",
                "searchTerms":[
                    "fa mouse alt"
                ]
            },
            {
                "title":"fa-mouse-pointer",
                "searchTerms":[
                    "fa mouse pointer"
                ]
            },
            {
                "title":"fa-mp3-player",
                "searchTerms":[
                    "fa mp3 player"
                ]
            },
            {
                "title":"fa-mug",
                "searchTerms":[
                    "fa mug"
                ]
            },
            {
                "title":"fa-mug-hot",
                "searchTerms":[
                    "fa mug hot"
                ]
            },
            {
                "title":"fa-mug-marshmallows",
                "searchTerms":[
                    "fa mug marshmallows"
                ]
            },
            {
                "title":"fa-mug-tea",
                "searchTerms":[
                    "fa mug tea"
                ]
            },
            {
                "title":"fa-music",
                "searchTerms":[
                    "fa music"
                ]
            },
            {
                "title":"fa-music-alt",
                "searchTerms":[
                    "fa music alt"
                ]
            },
            {
                "title":"fa-music-alt-slash",
                "searchTerms":[
                    "fa music alt slash"
                ]
            },
            {
                "title":"fa-music-slash",
                "searchTerms":[
                    "fa music slash"
                ]
            },
            {
                "title":"fa-napster",
                "searchTerms":[
                    "fa napster"
                ]
            },
            {
                "title":"fa-narwhal",
                "searchTerms":[
                    "fa narwhal"
                ]
            },
            {
                "title":"fa-neos",
                "searchTerms":[
                    "fa neos"
                ]
            },
            {
                "title":"fa-network-wired",
                "searchTerms":[
                    "fa network wired"
                ]
            },
            {
                "title":"fa-neuter",
                "searchTerms":[
                    "fa neuter"
                ]
            },
            {
                "title":"fa-newspaper",
                "searchTerms":[
                    "fa newspaper"
                ]
            },
            {
                "title":"fa-nimblr",
                "searchTerms":[
                    "fa nimblr"
                ]
            },
            {
                "title":"fa-node",
                "searchTerms":[
                    "fa node"
                ]
            },
            {
                "title":"fa-node-js",
                "searchTerms":[
                    "fa node js"
                ]
            },
            {
                "title":"fa-not-equal",
                "searchTerms":[
                    "fa not equal"
                ]
            },
            {
                "title":"fa-notes-medical",
                "searchTerms":[
                    "fa notes medical"
                ]
            },
            {
                "title":"fa-npm",
                "searchTerms":[
                    "fa npm"
                ]
            },
            {
                "title":"fa-ns8",
                "searchTerms":[
                    "fa ns8"
                ]
            },
            {
                "title":"fa-nutritionix",
                "searchTerms":[
                    "fa nutritionix"
                ]
            },
            {
                "title":"fa-object-group",
                "searchTerms":[
                    "fa object group"
                ]
            },
            {
                "title":"fa-object-ungroup",
                "searchTerms":[
                    "fa object ungroup"
                ]
            },
            {
                "title":"fa-octagon",
                "searchTerms":[
                    "fa octagon"
                ]
            },
            {
                "title":"fa-odnoklassniki",
                "searchTerms":[
                    "fa odnoklassniki"
                ]
            },
            {
                "title":"fa-odnoklassniki-square",
                "searchTerms":[
                    "fa odnoklassniki square"
                ]
            },
            {
                "title":"fa-oil-can",
                "searchTerms":[
                    "fa oil can"
                ]
            },
            {
                "title":"fa-oil-temp",
                "searchTerms":[
                    "fa oil temp"
                ]
            },
            {
                "title":"fa-old-republic",
                "searchTerms":[
                    "fa old republic"
                ]
            },
            {
                "title":"fa-om",
                "searchTerms":[
                    "fa om"
                ]
            },
            {
                "title":"fa-omega",
                "searchTerms":[
                    "fa omega"
                ]
            },
            {
                "title":"fa-opencart",
                "searchTerms":[
                    "fa opencart"
                ]
            },
            {
                "title":"fa-openid",
                "searchTerms":[
                    "fa openid"
                ]
            },
            {
                "title":"fa-opera",
                "searchTerms":[
                    "fa opera"
                ]
            },
            {
                "title":"fa-optin-monster",
                "searchTerms":[
                    "fa optin monster"
                ]
            },
            {
                "title":"fa-orcid",
                "searchTerms":[
                    "fa orcid"
                ]
            },
            {
                "title":"fa-ornament",
                "searchTerms":[
                    "fa ornament"
                ]
            },
            {
                "title":"fa-osi",
                "searchTerms":[
                    "fa osi"
                ]
            },
            {
                "title":"fa-otter",
                "searchTerms":[
                    "fa otter"
                ]
            },
            {
                "title":"fa-outdent",
                "searchTerms":[
                    "fa outdent"
                ]
            },
            {
                "title":"fa-overline",
                "searchTerms":[
                    "fa overline"
                ]
            },
            {
                "title":"fa-page-break",
                "searchTerms":[
                    "fa page break"
                ]
            },
            {
                "title":"fa-page4",
                "searchTerms":[
                    "fa page4"
                ]
            },
            {
                "title":"fa-pagelines",
                "searchTerms":[
                    "fa pagelines"
                ]
            },
            {
                "title":"fa-pager",
                "searchTerms":[
                    "fa pager"
                ]
            },
            {
                "title":"fa-paint-brush",
                "searchTerms":[
                    "fa paint brush"
                ]
            },
            {
                "title":"fa-paint-brush-alt",
                "searchTerms":[
                    "fa paint brush alt"
                ]
            },
            {
                "title":"fa-paint-roller",
                "searchTerms":[
                    "fa paint roller"
                ]
            },
            {
                "title":"fa-palette",
                "searchTerms":[
                    "fa palette"
                ]
            },
            {
                "title":"fa-palfed",
                "searchTerms":[
                    "fa palfed"
                ]
            },
            {
                "title":"fa-pallet",
                "searchTerms":[
                    "fa pallet"
                ]
            },
            {
                "title":"fa-pallet-alt",
                "searchTerms":[
                    "fa pallet alt"
                ]
            },
            {
                "title":"fa-paper-plane",
                "searchTerms":[
                    "fa paper plane"
                ]
            },
            {
                "title":"fa-paperclip",
                "searchTerms":[
                    "fa paperclip"
                ]
            },
            {
                "title":"fa-parachute-box",
                "searchTerms":[
                    "fa parachute box"
                ]
            },
            {
                "title":"fa-paragraph",
                "searchTerms":[
                    "fa paragraph"
                ]
            },
            {
                "title":"fa-paragraph-rtl",
                "searchTerms":[
                    "fa paragraph rtl"
                ]
            },
            {
                "title":"fa-parking",
                "searchTerms":[
                    "fa parking"
                ]
            },
            {
                "title":"fa-parking-circle",
                "searchTerms":[
                    "fa parking circle"
                ]
            },
            {
                "title":"fa-parking-circle-slash",
                "searchTerms":[
                    "fa parking circle slash"
                ]
            },
            {
                "title":"fa-parking-slash",
                "searchTerms":[
                    "fa parking slash"
                ]
            },
            {
                "title":"fa-passport",
                "searchTerms":[
                    "fa passport"
                ]
            },
            {
                "title":"fa-pastafarianism",
                "searchTerms":[
                    "fa pastafarianism"
                ]
            },
            {
                "title":"fa-paste",
                "searchTerms":[
                    "fa paste"
                ]
            },
            {
                "title":"fa-patreon",
                "searchTerms":[
                    "fa patreon"
                ]
            },
            {
                "title":"fa-pause",
                "searchTerms":[
                    "fa pause"
                ]
            },
            {
                "title":"fa-pause-circle",
                "searchTerms":[
                    "fa pause circle"
                ]
            },
            {
                "title":"fa-paw",
                "searchTerms":[
                    "fa paw"
                ]
            },
            {
                "title":"fa-paw-alt",
                "searchTerms":[
                    "fa paw alt"
                ]
            },
            {
                "title":"fa-paw-claws",
                "searchTerms":[
                    "fa paw claws"
                ]
            },
            {
                "title":"fa-paypal",
                "searchTerms":[
                    "fa paypal"
                ]
            },
            {
                "title":"fa-peace",
                "searchTerms":[
                    "fa peace"
                ]
            },
            {
                "title":"fa-pegasus",
                "searchTerms":[
                    "fa pegasus"
                ]
            },
            {
                "title":"fa-pen",
                "searchTerms":[
                    "fa pen"
                ]
            },
            {
                "title":"fa-pen-alt",
                "searchTerms":[
                    "fa pen alt"
                ]
            },
            {
                "title":"fa-pen-fancy",
                "searchTerms":[
                    "fa pen fancy"
                ]
            },
            {
                "title":"fa-pen-nib",
                "searchTerms":[
                    "fa pen nib"
                ]
            },
            {
                "title":"fa-pen-square",
                "searchTerms":[
                    "fa pen square"
                ]
            },
            {
                "title":"fa-pencil",
                "searchTerms":[
                    "fa pencil"
                ]
            },
            {
                "title":"fa-pencil-alt",
                "searchTerms":[
                    "fa pencil alt"
                ]
            },
            {
                "title":"fa-pencil-paintbrush",
                "searchTerms":[
                    "fa pencil paintbrush"
                ]
            },
            {
                "title":"fa-pencil-ruler",
                "searchTerms":[
                    "fa pencil ruler"
                ]
            },
            {
                "title":"fa-pennant",
                "searchTerms":[
                    "fa pennant"
                ]
            },
            {
                "title":"fa-penny-arcade",
                "searchTerms":[
                    "fa penny arcade"
                ]
            },
            {
                "title":"fa-people-carry",
                "searchTerms":[
                    "fa people carry"
                ]
            },
            {
                "title":"fa-pepper-hot",
                "searchTerms":[
                    "fa pepper hot"
                ]
            },
            {
                "title":"fa-percent",
                "searchTerms":[
                    "fa percent"
                ]
            },
            {
                "title":"fa-percentage",
                "searchTerms":[
                    "fa percentage"
                ]
            },
            {
                "title":"fa-periscope",
                "searchTerms":[
                    "fa periscope"
                ]
            },
            {
                "title":"fa-person-booth",
                "searchTerms":[
                    "fa person booth"
                ]
            },
            {
                "title":"fa-person-carry",
                "searchTerms":[
                    "fa person carry"
                ]
            },
            {
                "title":"fa-person-dolly",
                "searchTerms":[
                    "fa person dolly"
                ]
            },
            {
                "title":"fa-person-dolly-empty",
                "searchTerms":[
                    "fa person dolly empty"
                ]
            },
            {
                "title":"fa-person-sign",
                "searchTerms":[
                    "fa person sign"
                ]
            },
            {
                "title":"fa-phabricator",
                "searchTerms":[
                    "fa phabricator"
                ]
            },
            {
                "title":"fa-phoenix-framework",
                "searchTerms":[
                    "fa phoenix framework"
                ]
            },
            {
                "title":"fa-phoenix-squadron",
                "searchTerms":[
                    "fa phoenix squadron"
                ]
            },
            {
                "title":"fa-phone",
                "searchTerms":[
                    "fa phone"
                ]
            },
            {
                "title":"fa-phone-alt",
                "searchTerms":[
                    "fa phone alt"
                ]
            },
            {
                "title":"fa-phone-laptop",
                "searchTerms":[
                    "fa phone laptop"
                ]
            },
            {
                "title":"fa-phone-office",
                "searchTerms":[
                    "fa phone office"
                ]
            },
            {
                "title":"fa-phone-plus",
                "searchTerms":[
                    "fa phone plus"
                ]
            },
            {
                "title":"fa-phone-rotary",
                "searchTerms":[
                    "fa phone rotary"
                ]
            },
            {
                "title":"fa-phone-slash",
                "searchTerms":[
                    "fa phone slash"
                ]
            },
            {
                "title":"fa-phone-square",
                "searchTerms":[
                    "fa phone square"
                ]
            },
            {
                "title":"fa-phone-square-alt",
                "searchTerms":[
                    "fa phone square alt"
                ]
            },
            {
                "title":"fa-phone-volume",
                "searchTerms":[
                    "fa phone volume"
                ]
            },
            {
                "title":"fa-photo-video",
                "searchTerms":[
                    "fa photo video"
                ]
            },
            {
                "title":"fa-php",
                "searchTerms":[
                    "fa php"
                ]
            },
            {
                "title":"fa-pi",
                "searchTerms":[
                    "fa pi"
                ]
            },
            {
                "title":"fa-piano",
                "searchTerms":[
                    "fa piano"
                ]
            },
            {
                "title":"fa-piano-keyboard",
                "searchTerms":[
                    "fa piano keyboard"
                ]
            },
            {
                "title":"fa-pie",
                "searchTerms":[
                    "fa pie"
                ]
            },
            {
                "title":"fa-pied-piper",
                "searchTerms":[
                    "fa pied piper"
                ]
            },
            {
                "title":"fa-pied-piper-alt",
                "searchTerms":[
                    "fa pied piper alt"
                ]
            },
            {
                "title":"fa-pied-piper-hat",
                "searchTerms":[
                    "fa pied piper hat"
                ]
            },
            {
                "title":"fa-pied-piper-pp",
                "searchTerms":[
                    "fa pied piper pp"
                ]
            },
            {
                "title":"fa-pig",
                "searchTerms":[
                    "fa pig"
                ]
            },
            {
                "title":"fa-piggy-bank",
                "searchTerms":[
                    "fa piggy bank"
                ]
            },
            {
                "title":"fa-pills",
                "searchTerms":[
                    "fa pills"
                ]
            },
            {
                "title":"fa-pinterest",
                "searchTerms":[
                    "fa pinterest"
                ]
            },
            {
                "title":"fa-pinterest-p",
                "searchTerms":[
                    "fa pinterest p"
                ]
            },
            {
                "title":"fa-pinterest-square",
                "searchTerms":[
                    "fa pinterest square"
                ]
            },
            {
                "title":"fa-pizza",
                "searchTerms":[
                    "fa pizza"
                ]
            },
            {
                "title":"fa-pizza-slice",
                "searchTerms":[
                    "fa pizza slice"
                ]
            },
            {
                "title":"fa-place-of-worship",
                "searchTerms":[
                    "fa place of worship"
                ]
            },
            {
                "title":"fa-plane",
                "searchTerms":[
                    "fa plane"
                ]
            },
            {
                "title":"fa-plane-alt",
                "searchTerms":[
                    "fa plane alt"
                ]
            },
            {
                "title":"fa-plane-arrival",
                "searchTerms":[
                    "fa plane arrival"
                ]
            },
            {
                "title":"fa-plane-departure",
                "searchTerms":[
                    "fa plane departure"
                ]
            },
            {
                "title":"fa-play",
                "searchTerms":[
                    "fa play"
                ]
            },
            {
                "title":"fa-play-circle",
                "searchTerms":[
                    "fa play circle"
                ]
            },
            {
                "title":"fa-playstation",
                "searchTerms":[
                    "fa playstation"
                ]
            },
            {
                "title":"fa-plug",
                "searchTerms":[
                    "fa plug"
                ]
            },
            {
                "title":"fa-plus",
                "searchTerms":[
                    "fa plus"
                ]
            },
            {
                "title":"fa-plus-circle",
                "searchTerms":[
                    "fa plus circle"
                ]
            },
            {
                "title":"fa-plus-hexagon",
                "searchTerms":[
                    "fa plus hexagon"
                ]
            },
            {
                "title":"fa-plus-octagon",
                "searchTerms":[
                    "fa plus octagon"
                ]
            },
            {
                "title":"fa-plus-square",
                "searchTerms":[
                    "fa plus square"
                ]
            },
            {
                "title":"fa-podcast",
                "searchTerms":[
                    "fa podcast"
                ]
            },
            {
                "title":"fa-podium",
                "searchTerms":[
                    "fa podium"
                ]
            },
            {
                "title":"fa-podium-star",
                "searchTerms":[
                    "fa podium star"
                ]
            },
            {
                "title":"fa-poll",
                "searchTerms":[
                    "fa poll"
                ]
            },
            {
                "title":"fa-poll-h",
                "searchTerms":[
                    "fa poll h"
                ]
            },
            {
                "title":"fa-poll-people",
                "searchTerms":[
                    "fa poll people"
                ]
            },
            {
                "title":"fa-poo",
                "searchTerms":[
                    "fa poo"
                ]
            },
            {
                "title":"fa-poo-storm",
                "searchTerms":[
                    "fa poo storm"
                ]
            },
            {
                "title":"fa-poop",
                "searchTerms":[
                    "fa poop"
                ]
            },
            {
                "title":"fa-popcorn",
                "searchTerms":[
                    "fa popcorn"
                ]
            },
            {
                "title":"fa-portrait",
                "searchTerms":[
                    "fa portrait"
                ]
            },
            {
                "title":"fa-pound-sign",
                "searchTerms":[
                    "fa pound sign"
                ]
            },
            {
                "title":"fa-power-off",
                "searchTerms":[
                    "fa power off"
                ]
            },
            {
                "title":"fa-pray",
                "searchTerms":[
                    "fa pray"
                ]
            },
            {
                "title":"fa-praying-hands",
                "searchTerms":[
                    "fa praying hands"
                ]
            },
            {
                "title":"fa-prescription",
                "searchTerms":[
                    "fa prescription"
                ]
            },
            {
                "title":"fa-prescription-bottle",
                "searchTerms":[
                    "fa prescription bottle"
                ]
            },
            {
                "title":"fa-prescription-bottle-alt",
                "searchTerms":[
                    "fa prescription bottle alt"
                ]
            },
            {
                "title":"fa-presentation",
                "searchTerms":[
                    "fa presentation"
                ]
            },
            {
                "title":"fa-print",
                "searchTerms":[
                    "fa print"
                ]
            },
            {
                "title":"fa-print-search",
                "searchTerms":[
                    "fa print search"
                ]
            },
            {
                "title":"fa-print-slash",
                "searchTerms":[
                    "fa print slash"
                ]
            },
            {
                "title":"fa-procedures",
                "searchTerms":[
                    "fa procedures"
                ]
            },
            {
                "title":"fa-product-hunt",
                "searchTerms":[
                    "fa product hunt"
                ]
            },
            {
                "title":"fa-project-diagram",
                "searchTerms":[
                    "fa project diagram"
                ]
            },
            {
                "title":"fa-projector",
                "searchTerms":[
                    "fa projector"
                ]
            },
            {
                "title":"fa-pumpkin",
                "searchTerms":[
                    "fa pumpkin"
                ]
            },
            {
                "title":"fa-pushed",
                "searchTerms":[
                    "fa pushed"
                ]
            },
            {
                "title":"fa-puzzle-piece",
                "searchTerms":[
                    "fa puzzle piece"
                ]
            },
            {
                "title":"fa-python",
                "searchTerms":[
                    "fa python"
                ]
            },
            {
                "title":"fa-qq",
                "searchTerms":[
                    "fa qq"
                ]
            },
            {
                "title":"fa-qrcode",
                "searchTerms":[
                    "fa qrcode"
                ]
            },
            {
                "title":"fa-question",
                "searchTerms":[
                    "fa question"
                ]
            },
            {
                "title":"fa-question-circle",
                "searchTerms":[
                    "fa question circle"
                ]
            },
            {
                "title":"fa-question-square",
                "searchTerms":[
                    "fa question square"
                ]
            },
            {
                "title":"fa-quidditch",
                "searchTerms":[
                    "fa quidditch"
                ]
            },
            {
                "title":"fa-quinscape",
                "searchTerms":[
                    "fa quinscape"
                ]
            },
            {
                "title":"fa-quora",
                "searchTerms":[
                    "fa quora"
                ]
            },
            {
                "title":"fa-quote-left",
                "searchTerms":[
                    "fa quote left"
                ]
            },
            {
                "title":"fa-quote-right",
                "searchTerms":[
                    "fa quote right"
                ]
            },
            {
                "title":"fa-quran",
                "searchTerms":[
                    "fa quran"
                ]
            },
            {
                "title":"fa-r-project",
                "searchTerms":[
                    "fa r project"
                ]
            },
            {
                "title":"fa-rabbit",
                "searchTerms":[
                    "fa rabbit"
                ]
            },
            {
                "title":"fa-rabbit-fast",
                "searchTerms":[
                    "fa rabbit fast"
                ]
            },
            {
                "title":"fa-racquet",
                "searchTerms":[
                    "fa racquet"
                ]
            },
            {
                "title":"fa-radiation",
                "searchTerms":[
                    "fa radiation"
                ]
            },
            {
                "title":"fa-radiation-alt",
                "searchTerms":[
                    "fa radiation alt"
                ]
            },
            {
                "title":"fa-radio",
                "searchTerms":[
                    "fa radio"
                ]
            },
            {
                "title":"fa-radio-alt",
                "searchTerms":[
                    "fa radio alt"
                ]
            },
            {
                "title":"fa-rainbow",
                "searchTerms":[
                    "fa rainbow"
                ]
            },
            {
                "title":"fa-raindrops",
                "searchTerms":[
                    "fa raindrops"
                ]
            },
            {
                "title":"fa-ram",
                "searchTerms":[
                    "fa ram"
                ]
            },
            {
                "title":"fa-ramp-loading",
                "searchTerms":[
                    "fa ramp loading"
                ]
            },
            {
                "title":"fa-random",
                "searchTerms":[
                    "fa random"
                ]
            },
            {
                "title":"fa-raspberry-pi",
                "searchTerms":[
                    "fa raspberry pi"
                ]
            },
            {
                "title":"fa-ravelry",
                "searchTerms":[
                    "fa ravelry"
                ]
            },
            {
                "title":"fa-react",
                "searchTerms":[
                    "fa react"
                ]
            },
            {
                "title":"fa-reacteurope",
                "searchTerms":[
                    "fa reacteurope"
                ]
            },
            {
                "title":"fa-readme",
                "searchTerms":[
                    "fa readme"
                ]
            },
            {
                "title":"fa-rebel",
                "searchTerms":[
                    "fa rebel"
                ]
            },
            {
                "title":"fa-receipt",
                "searchTerms":[
                    "fa receipt"
                ]
            },
            {
                "title":"fa-record-vinyl",
                "searchTerms":[
                    "fa record vinyl"
                ]
            },
            {
                "title":"fa-rectangle-landscape",
                "searchTerms":[
                    "fa rectangle landscape"
                ]
            },
            {
                "title":"fa-rectangle-portrait",
                "searchTerms":[
                    "fa rectangle portrait"
                ]
            },
            {
                "title":"fa-rectangle-wide",
                "searchTerms":[
                    "fa rectangle wide"
                ]
            },
            {
                "title":"fa-recycle",
                "searchTerms":[
                    "fa recycle"
                ]
            },
            {
                "title":"fa-red-river",
                "searchTerms":[
                    "fa red river"
                ]
            },
            {
                "title":"fa-reddit",
                "searchTerms":[
                    "fa reddit"
                ]
            },
            {
                "title":"fa-reddit-alien",
                "searchTerms":[
                    "fa reddit alien"
                ]
            },
            {
                "title":"fa-reddit-square",
                "searchTerms":[
                    "fa reddit square"
                ]
            },
            {
                "title":"fa-redhat",
                "searchTerms":[
                    "fa redhat"
                ]
            },
            {
                "title":"fa-redo",
                "searchTerms":[
                    "fa redo"
                ]
            },
            {
                "title":"fa-redo-alt",
                "searchTerms":[
                    "fa redo alt"
                ]
            },
            {
                "title":"fa-registered",
                "searchTerms":[
                    "fa registered"
                ]
            },
            {
                "title":"fa-remove-format",
                "searchTerms":[
                    "fa remove format"
                ]
            },
            {
                "title":"fa-renren",
                "searchTerms":[
                    "fa renren"
                ]
            },
            {
                "title":"fa-repeat",
                "searchTerms":[
                    "fa repeat"
                ]
            },
            {
                "title":"fa-repeat-1",
                "searchTerms":[
                    "fa repeat 1"
                ]
            },
            {
                "title":"fa-repeat-1-alt",
                "searchTerms":[
                    "fa repeat 1 alt"
                ]
            },
            {
                "title":"fa-repeat-alt",
                "searchTerms":[
                    "fa repeat alt"
                ]
            },
            {
                "title":"fa-reply",
                "searchTerms":[
                    "fa reply"
                ]
            },
            {
                "title":"fa-reply-all",
                "searchTerms":[
                    "fa reply all"
                ]
            },
            {
                "title":"fa-replyd",
                "searchTerms":[
                    "fa replyd"
                ]
            },
            {
                "title":"fa-republican",
                "searchTerms":[
                    "fa republican"
                ]
            },
            {
                "title":"fa-researchgate",
                "searchTerms":[
                    "fa researchgate"
                ]
            },
            {
                "title":"fa-resolving",
                "searchTerms":[
                    "fa resolving"
                ]
            },
            {
                "title":"fa-restroom",
                "searchTerms":[
                    "fa restroom"
                ]
            },
            {
                "title":"fa-retweet",
                "searchTerms":[
                    "fa retweet"
                ]
            },
            {
                "title":"fa-retweet-alt",
                "searchTerms":[
                    "fa retweet alt"
                ]
            },
            {
                "title":"fa-rev",
                "searchTerms":[
                    "fa rev"
                ]
            },
            {
                "title":"fa-ribbon",
                "searchTerms":[
                    "fa ribbon"
                ]
            },
            {
                "title":"fa-ring",
                "searchTerms":[
                    "fa ring"
                ]
            },
            {
                "title":"fa-rings-wedding",
                "searchTerms":[
                    "fa rings wedding"
                ]
            },
            {
                "title":"fa-road",
                "searchTerms":[
                    "fa road"
                ]
            },
            {
                "title":"fa-robot",
                "searchTerms":[
                    "fa robot"
                ]
            },
            {
                "title":"fa-rocket",
                "searchTerms":[
                    "fa rocket"
                ]
            },
            {
                "title":"fa-rocketchat",
                "searchTerms":[
                    "fa rocketchat"
                ]
            },
            {
                "title":"fa-rockrms",
                "searchTerms":[
                    "fa rockrms"
                ]
            },
            {
                "title":"fa-route",
                "searchTerms":[
                    "fa route"
                ]
            },
            {
                "title":"fa-route-highway",
                "searchTerms":[
                    "fa route highway"
                ]
            },
            {
                "title":"fa-route-interstate",
                "searchTerms":[
                    "fa route interstate"
                ]
            },
            {
                "title":"fa-router",
                "searchTerms":[
                    "fa router"
                ]
            },
            {
                "title":"fa-rss",
                "searchTerms":[
                    "fa rss"
                ]
            },
            {
                "title":"fa-rss-square",
                "searchTerms":[
                    "fa rss square"
                ]
            },
            {
                "title":"fa-ruble-sign",
                "searchTerms":[
                    "fa ruble sign"
                ]
            },
            {
                "title":"fa-ruler",
                "searchTerms":[
                    "fa ruler"
                ]
            },
            {
                "title":"fa-ruler-combined",
                "searchTerms":[
                    "fa ruler combined"
                ]
            },
            {
                "title":"fa-ruler-horizontal",
                "searchTerms":[
                    "fa ruler horizontal"
                ]
            },
            {
                "title":"fa-ruler-triangle",
                "searchTerms":[
                    "fa ruler triangle"
                ]
            },
            {
                "title":"fa-ruler-vertical",
                "searchTerms":[
                    "fa ruler vertical"
                ]
            },
            {
                "title":"fa-running",
                "searchTerms":[
                    "fa running"
                ]
            },
            {
                "title":"fa-rupee-sign",
                "searchTerms":[
                    "fa rupee sign"
                ]
            },
            {
                "title":"fa-rv",
                "searchTerms":[
                    "fa rv"
                ]
            },
            {
                "title":"fa-sack",
                "searchTerms":[
                    "fa sack"
                ]
            },
            {
                "title":"fa-sack-dollar",
                "searchTerms":[
                    "fa sack dollar"
                ]
            },
            {
                "title":"fa-sad-cry",
                "searchTerms":[
                    "fa sad cry"
                ]
            },
            {
                "title":"fa-sad-tear",
                "searchTerms":[
                    "fa sad tear"
                ]
            },
            {
                "title":"fa-safari",
                "searchTerms":[
                    "fa safari"
                ]
            },
            {
                "title":"fa-salad",
                "searchTerms":[
                    "fa salad"
                ]
            },
            {
                "title":"fa-salesforce",
                "searchTerms":[
                    "fa salesforce"
                ]
            },
            {
                "title":"fa-sandwich",
                "searchTerms":[
                    "fa sandwich"
                ]
            },
            {
                "title":"fa-sass",
                "searchTerms":[
                    "fa sass"
                ]
            },
            {
                "title":"fa-satellite",
                "searchTerms":[
                    "fa satellite"
                ]
            },
            {
                "title":"fa-satellite-dish",
                "searchTerms":[
                    "fa satellite dish"
                ]
            },
            {
                "title":"fa-sausage",
                "searchTerms":[
                    "fa sausage"
                ]
            },
            {
                "title":"fa-save",
                "searchTerms":[
                    "fa save"
                ]
            },
            {
                "title":"fa-sax-hot",
                "searchTerms":[
                    "fa sax hot"
                ]
            },
            {
                "title":"fa-saxophone",
                "searchTerms":[
                    "fa saxophone"
                ]
            },
            {
                "title":"fa-scalpel",
                "searchTerms":[
                    "fa scalpel"
                ]
            },
            {
                "title":"fa-scalpel-path",
                "searchTerms":[
                    "fa scalpel path"
                ]
            },
            {
                "title":"fa-scanner",
                "searchTerms":[
                    "fa scanner"
                ]
            },
            {
                "title":"fa-scanner-image",
                "searchTerms":[
                    "fa scanner image"
                ]
            },
            {
                "title":"fa-scanner-keyboard",
                "searchTerms":[
                    "fa scanner keyboard"
                ]
            },
            {
                "title":"fa-scanner-touchscreen",
                "searchTerms":[
                    "fa scanner touchscreen"
                ]
            },
            {
                "title":"fa-scarecrow",
                "searchTerms":[
                    "fa scarecrow"
                ]
            },
            {
                "title":"fa-scarf",
                "searchTerms":[
                    "fa scarf"
                ]
            },
            {
                "title":"fa-schlix",
                "searchTerms":[
                    "fa schlix"
                ]
            },
            {
                "title":"fa-school",
                "searchTerms":[
                    "fa school"
                ]
            },
            {
                "title":"fa-screwdriver",
                "searchTerms":[
                    "fa screwdriver"
                ]
            },
            {
                "title":"fa-scribd",
                "searchTerms":[
                    "fa scribd"
                ]
            },
            {
                "title":"fa-scroll",
                "searchTerms":[
                    "fa scroll"
                ]
            },
            {
                "title":"fa-scroll-old",
                "searchTerms":[
                    "fa scroll old"
                ]
            },
            {
                "title":"fa-scrubber",
                "searchTerms":[
                    "fa scrubber"
                ]
            },
            {
                "title":"fa-scythe",
                "searchTerms":[
                    "fa scythe"
                ]
            },
            {
                "title":"fa-sd-card",
                "searchTerms":[
                    "fa sd card"
                ]
            },
            {
                "title":"fa-search",
                "searchTerms":[
                    "fa search"
                ]
            },
            {
                "title":"fa-search-dollar",
                "searchTerms":[
                    "fa search dollar"
                ]
            },
            {
                "title":"fa-search-location",
                "searchTerms":[
                    "fa search location"
                ]
            },
            {
                "title":"fa-search-minus",
                "searchTerms":[
                    "fa search minus"
                ]
            },
            {
                "title":"fa-search-plus",
                "searchTerms":[
                    "fa search plus"
                ]
            },
            {
                "title":"fa-searchengin",
                "searchTerms":[
                    "fa searchengin"
                ]
            },
            {
                "title":"fa-seedling",
                "searchTerms":[
                    "fa seedling"
                ]
            },
            {
                "title":"fa-sellcast",
                "searchTerms":[
                    "fa sellcast"
                ]
            },
            {
                "title":"fa-sellsy",
                "searchTerms":[
                    "fa sellsy"
                ]
            },
            {
                "title":"fa-send-back",
                "searchTerms":[
                    "fa send back"
                ]
            },
            {
                "title":"fa-send-backward",
                "searchTerms":[
                    "fa send backward"
                ]
            },
            {
                "title":"fa-server",
                "searchTerms":[
                    "fa server"
                ]
            },
            {
                "title":"fa-servicestack",
                "searchTerms":[
                    "fa servicestack"
                ]
            },
            {
                "title":"fa-shapes",
                "searchTerms":[
                    "fa shapes"
                ]
            },
            {
                "title":"fa-share",
                "searchTerms":[
                    "fa share"
                ]
            },
            {
                "title":"fa-share-all",
                "searchTerms":[
                    "fa share all"
                ]
            },
            {
                "title":"fa-share-alt",
                "searchTerms":[
                    "fa share alt"
                ]
            },
            {
                "title":"fa-share-alt-square",
                "searchTerms":[
                    "fa share alt square"
                ]
            },
            {
                "title":"fa-share-square",
                "searchTerms":[
                    "fa share square"
                ]
            },
            {
                "title":"fa-sheep",
                "searchTerms":[
                    "fa sheep"
                ]
            },
            {
                "title":"fa-shekel-sign",
                "searchTerms":[
                    "fa shekel sign"
                ]
            },
            {
                "title":"fa-shield",
                "searchTerms":[
                    "fa shield"
                ]
            },
            {
                "title":"fa-shield-alt",
                "searchTerms":[
                    "fa shield alt"
                ]
            },
            {
                "title":"fa-shield-check",
                "searchTerms":[
                    "fa shield check"
                ]
            },
            {
                "title":"fa-shield-cross",
                "searchTerms":[
                    "fa shield cross"
                ]
            },
            {
                "title":"fa-ship",
                "searchTerms":[
                    "fa ship"
                ]
            },
            {
                "title":"fa-shipping-fast",
                "searchTerms":[
                    "fa shipping fast"
                ]
            },
            {
                "title":"fa-shipping-timed",
                "searchTerms":[
                    "fa shipping timed"
                ]
            },
            {
                "title":"fa-shirtsinbulk",
                "searchTerms":[
                    "fa shirtsinbulk"
                ]
            },
            {
                "title":"fa-shish-kebab",
                "searchTerms":[
                    "fa shish kebab"
                ]
            },
            {
                "title":"fa-shoe-prints",
                "searchTerms":[
                    "fa shoe prints"
                ]
            },
            {
                "title":"fa-shopping-bag",
                "searchTerms":[
                    "fa shopping bag"
                ]
            },
            {
                "title":"fa-shopping-basket",
                "searchTerms":[
                    "fa shopping basket"
                ]
            },
            {
                "title":"fa-shopping-cart",
                "searchTerms":[
                    "fa shopping cart"
                ]
            },
            {
                "title":"fa-shopware",
                "searchTerms":[
                    "fa shopware"
                ]
            },
            {
                "title":"fa-shovel",
                "searchTerms":[
                    "fa shovel"
                ]
            },
            {
                "title":"fa-shovel-snow",
                "searchTerms":[
                    "fa shovel snow"
                ]
            },
            {
                "title":"fa-shower",
                "searchTerms":[
                    "fa shower"
                ]
            },
            {
                "title":"fa-shredder",
                "searchTerms":[
                    "fa shredder"
                ]
            },
            {
                "title":"fa-shuttle-van",
                "searchTerms":[
                    "fa shuttle van"
                ]
            },
            {
                "title":"fa-shuttlecock",
                "searchTerms":[
                    "fa shuttlecock"
                ]
            },
            {
                "title":"fa-sickle",
                "searchTerms":[
                    "fa sickle"
                ]
            },
            {
                "title":"fa-sigma",
                "searchTerms":[
                    "fa sigma"
                ]
            },
            {
                "title":"fa-sign",
                "searchTerms":[
                    "fa sign"
                ]
            },
            {
                "title":"fa-sign-in",
                "searchTerms":[
                    "fa sign in"
                ]
            },
            {
                "title":"fa-sign-in-alt",
                "searchTerms":[
                    "fa sign in alt"
                ]
            },
            {
                "title":"fa-sign-language",
                "searchTerms":[
                    "fa sign language"
                ]
            },
            {
                "title":"fa-sign-out",
                "searchTerms":[
                    "fa sign out"
                ]
            },
            {
                "title":"fa-sign-out-alt",
                "searchTerms":[
                    "fa sign out alt"
                ]
            },
            {
                "title":"fa-signal",
                "searchTerms":[
                    "fa signal"
                ]
            },
            {
                "title":"fa-signal-1",
                "searchTerms":[
                    "fa signal 1"
                ]
            },
            {
                "title":"fa-signal-2",
                "searchTerms":[
                    "fa signal 2"
                ]
            },
            {
                "title":"fa-signal-3",
                "searchTerms":[
                    "fa signal 3"
                ]
            },
            {
                "title":"fa-signal-4",
                "searchTerms":[
                    "fa signal 4"
                ]
            },
            {
                "title":"fa-signal-alt",
                "searchTerms":[
                    "fa signal alt"
                ]
            },
            {
                "title":"fa-signal-alt-1",
                "searchTerms":[
                    "fa signal alt 1"
                ]
            },
            {
                "title":"fa-signal-alt-2",
                "searchTerms":[
                    "fa signal alt 2"
                ]
            },
            {
                "title":"fa-signal-alt-3",
                "searchTerms":[
                    "fa signal alt 3"
                ]
            },
            {
                "title":"fa-signal-alt-slash",
                "searchTerms":[
                    "fa signal alt slash"
                ]
            },
            {
                "title":"fa-signal-slash",
                "searchTerms":[
                    "fa signal slash"
                ]
            },
            {
                "title":"fa-signal-stream",
                "searchTerms":[
                    "fa signal stream"
                ]
            },
            {
                "title":"fa-signature",
                "searchTerms":[
                    "fa signature"
                ]
            },
            {
                "title":"fa-sim-card",
                "searchTerms":[
                    "fa sim card"
                ]
            },
            {
                "title":"fa-simplybuilt",
                "searchTerms":[
                    "fa simplybuilt"
                ]
            },
            {
                "title":"fa-sistrix",
                "searchTerms":[
                    "fa sistrix"
                ]
            },
            {
                "title":"fa-sitemap",
                "searchTerms":[
                    "fa sitemap"
                ]
            },
            {
                "title":"fa-sith",
                "searchTerms":[
                    "fa sith"
                ]
            },
            {
                "title":"fa-skating",
                "searchTerms":[
                    "fa skating"
                ]
            },
            {
                "title":"fa-skeleton",
                "searchTerms":[
                    "fa skeleton"
                ]
            },
            {
                "title":"fa-sketch",
                "searchTerms":[
                    "fa sketch"
                ]
            },
            {
                "title":"fa-ski-jump",
                "searchTerms":[
                    "fa ski jump"
                ]
            },
            {
                "title":"fa-ski-lift",
                "searchTerms":[
                    "fa ski lift"
                ]
            },
            {
                "title":"fa-skiing",
                "searchTerms":[
                    "fa skiing"
                ]
            },
            {
                "title":"fa-skiing-nordic",
                "searchTerms":[
                    "fa skiing nordic"
                ]
            },
            {
                "title":"fa-skull",
                "searchTerms":[
                    "fa skull"
                ]
            },
            {
                "title":"fa-skull-cow",
                "searchTerms":[
                    "fa skull cow"
                ]
            },
            {
                "title":"fa-skull-crossbones",
                "searchTerms":[
                    "fa skull crossbones"
                ]
            },
            {
                "title":"fa-skyatlas",
                "searchTerms":[
                    "fa skyatlas"
                ]
            },
            {
                "title":"fa-skype",
                "searchTerms":[
                    "fa skype"
                ]
            },
            {
                "title":"fa-slack",
                "searchTerms":[
                    "fa slack"
                ]
            },
            {
                "title":"fa-slack-hash",
                "searchTerms":[
                    "fa slack hash"
                ]
            },
            {
                "title":"fa-slash",
                "searchTerms":[
                    "fa slash"
                ]
            },
            {
                "title":"fa-sledding",
                "searchTerms":[
                    "fa sledding"
                ]
            },
            {
                "title":"fa-sleigh",
                "searchTerms":[
                    "fa sleigh"
                ]
            },
            {
                "title":"fa-sliders-h",
                "searchTerms":[
                    "fa sliders h"
                ]
            },
            {
                "title":"fa-sliders-h-square",
                "searchTerms":[
                    "fa sliders h square"
                ]
            },
            {
                "title":"fa-sliders-v",
                "searchTerms":[
                    "fa sliders v"
                ]
            },
            {
                "title":"fa-sliders-v-square",
                "searchTerms":[
                    "fa sliders v square"
                ]
            },
            {
                "title":"fa-slideshare",
                "searchTerms":[
                    "fa slideshare"
                ]
            },
            {
                "title":"fa-smile",
                "searchTerms":[
                    "fa smile"
                ]
            },
            {
                "title":"fa-smile-beam",
                "searchTerms":[
                    "fa smile beam"
                ]
            },
            {
                "title":"fa-smile-plus",
                "searchTerms":[
                    "fa smile plus"
                ]
            },
            {
                "title":"fa-smile-wink",
                "searchTerms":[
                    "fa smile wink"
                ]
            },
            {
                "title":"fa-smog",
                "searchTerms":[
                    "fa smog"
                ]
            },
            {
                "title":"fa-smoke",
                "searchTerms":[
                    "fa smoke"
                ]
            },
            {
                "title":"fa-smoking",
                "searchTerms":[
                    "fa smoking"
                ]
            },
            {
                "title":"fa-smoking-ban",
                "searchTerms":[
                    "fa smoking ban"
                ]
            },
            {
                "title":"fa-sms",
                "searchTerms":[
                    "fa sms"
                ]
            },
            {
                "title":"fa-snake",
                "searchTerms":[
                    "fa snake"
                ]
            },
            {
                "title":"fa-snapchat",
                "searchTerms":[
                    "fa snapchat"
                ]
            },
            {
                "title":"fa-snapchat-ghost",
                "searchTerms":[
                    "fa snapchat ghost"
                ]
            },
            {
                "title":"fa-snapchat-square",
                "searchTerms":[
                    "fa snapchat square"
                ]
            },
            {
                "title":"fa-snooze",
                "searchTerms":[
                    "fa snooze"
                ]
            },
            {
                "title":"fa-snow-blowing",
                "searchTerms":[
                    "fa snow blowing"
                ]
            },
            {
                "title":"fa-snowboarding",
                "searchTerms":[
                    "fa snowboarding"
                ]
            },
            {
                "title":"fa-snowflake",
                "searchTerms":[
                    "fa snowflake"
                ]
            },
            {
                "title":"fa-snowflakes",
                "searchTerms":[
                    "fa snowflakes"
                ]
            },
            {
                "title":"fa-snowman",
                "searchTerms":[
                    "fa snowman"
                ]
            },
            {
                "title":"fa-snowmobile",
                "searchTerms":[
                    "fa snowmobile"
                ]
            },
            {
                "title":"fa-snowplow",
                "searchTerms":[
                    "fa snowplow"
                ]
            },
            {
                "title":"fa-socks",
                "searchTerms":[
                    "fa socks"
                ]
            },
            {
                "title":"fa-solar-panel",
                "searchTerms":[
                    "fa solar panel"
                ]
            },
            {
                "title":"fa-sort",
                "searchTerms":[
                    "fa sort"
                ]
            },
            {
                "title":"fa-sort-alpha-down",
                "searchTerms":[
                    "fa sort alpha down"
                ]
            },
            {
                "title":"fa-sort-alpha-down-alt",
                "searchTerms":[
                    "fa sort alpha down alt"
                ]
            },
            {
                "title":"fa-sort-alpha-up",
                "searchTerms":[
                    "fa sort alpha up"
                ]
            },
            {
                "title":"fa-sort-alpha-up-alt",
                "searchTerms":[
                    "fa sort alpha up alt"
                ]
            },
            {
                "title":"fa-sort-alt",
                "searchTerms":[
                    "fa sort alt"
                ]
            },
            {
                "title":"fa-sort-amount-down",
                "searchTerms":[
                    "fa sort amount down"
                ]
            },
            {
                "title":"fa-sort-amount-down-alt",
                "searchTerms":[
                    "fa sort amount down alt"
                ]
            },
            {
                "title":"fa-sort-amount-up",
                "searchTerms":[
                    "fa sort amount up"
                ]
            },
            {
                "title":"fa-sort-amount-up-alt",
                "searchTerms":[
                    "fa sort amount up alt"
                ]
            },
            {
                "title":"fa-sort-down",
                "searchTerms":[
                    "fa sort down"
                ]
            },
            {
                "title":"fa-sort-numeric-down",
                "searchTerms":[
                    "fa sort numeric down"
                ]
            },
            {
                "title":"fa-sort-numeric-down-alt",
                "searchTerms":[
                    "fa sort numeric down alt"
                ]
            },
            {
                "title":"fa-sort-numeric-up",
                "searchTerms":[
                    "fa sort numeric up"
                ]
            },
            {
                "title":"fa-sort-numeric-up-alt",
                "searchTerms":[
                    "fa sort numeric up alt"
                ]
            },
            {
                "title":"fa-sort-shapes-down",
                "searchTerms":[
                    "fa sort shapes down"
                ]
            },
            {
                "title":"fa-sort-shapes-down-alt",
                "searchTerms":[
                    "fa sort shapes down alt"
                ]
            },
            {
                "title":"fa-sort-shapes-up",
                "searchTerms":[
                    "fa sort shapes up"
                ]
            },
            {
                "title":"fa-sort-shapes-up-alt",
                "searchTerms":[
                    "fa sort shapes up alt"
                ]
            },
            {
                "title":"fa-sort-size-down",
                "searchTerms":[
                    "fa sort size down"
                ]
            },
            {
                "title":"fa-sort-size-down-alt",
                "searchTerms":[
                    "fa sort size down alt"
                ]
            },
            {
                "title":"fa-sort-size-up",
                "searchTerms":[
                    "fa sort size up"
                ]
            },
            {
                "title":"fa-sort-size-up-alt",
                "searchTerms":[
                    "fa sort size up alt"
                ]
            },
            {
                "title":"fa-sort-up",
                "searchTerms":[
                    "fa sort up"
                ]
            },
            {
                "title":"fa-soundcloud",
                "searchTerms":[
                    "fa soundcloud"
                ]
            },
            {
                "title":"fa-soup",
                "searchTerms":[
                    "fa soup"
                ]
            },
            {
                "title":"fa-sourcetree",
                "searchTerms":[
                    "fa sourcetree"
                ]
            },
            {
                "title":"fa-spa",
                "searchTerms":[
                    "fa spa"
                ]
            },
            {
                "title":"fa-space-shuttle",
                "searchTerms":[
                    "fa space shuttle"
                ]
            },
            {
                "title":"fa-spade",
                "searchTerms":[
                    "fa spade"
                ]
            },
            {
                "title":"fa-sparkles",
                "searchTerms":[
                    "fa sparkles"
                ]
            },
            {
                "title":"fa-speakap",
                "searchTerms":[
                    "fa speakap"
                ]
            },
            {
                "title":"fa-speaker",
                "searchTerms":[
                    "fa speaker"
                ]
            },
            {
                "title":"fa-speaker-deck",
                "searchTerms":[
                    "fa speaker deck"
                ]
            },
            {
                "title":"fa-speakers",
                "searchTerms":[
                    "fa speakers"
                ]
            },
            {
                "title":"fa-spell-check",
                "searchTerms":[
                    "fa spell check"
                ]
            },
            {
                "title":"fa-spider",
                "searchTerms":[
                    "fa spider"
                ]
            },
            {
                "title":"fa-spider-black-widow",
                "searchTerms":[
                    "fa spider black widow"
                ]
            },
            {
                "title":"fa-spider-web",
                "searchTerms":[
                    "fa spider web"
                ]
            },
            {
                "title":"fa-spinner",
                "searchTerms":[
                    "fa spinner"
                ]
            },
            {
                "title":"fa-spinner-third",
                "searchTerms":[
                    "fa spinner third"
                ]
            },
            {
                "title":"fa-splotch",
                "searchTerms":[
                    "fa splotch"
                ]
            },
            {
                "title":"fa-spotify",
                "searchTerms":[
                    "fa spotify"
                ]
            },
            {
                "title":"fa-spray-can",
                "searchTerms":[
                    "fa spray can"
                ]
            },
            {
                "title":"fa-square",
                "searchTerms":[
                    "fa square"
                ]
            },
            {
                "title":"fa-square-full",
                "searchTerms":[
                    "fa square full"
                ]
            },
            {
                "title":"fa-square-root",
                "searchTerms":[
                    "fa square root"
                ]
            },
            {
                "title":"fa-square-root-alt",
                "searchTerms":[
                    "fa square root alt"
                ]
            },
            {
                "title":"fa-squarespace",
                "searchTerms":[
                    "fa squarespace"
                ]
            },
            {
                "title":"fa-squirrel",
                "searchTerms":[
                    "fa squirrel"
                ]
            },
            {
                "title":"fa-stack-exchange",
                "searchTerms":[
                    "fa stack exchange"
                ]
            },
            {
                "title":"fa-stack-overflow",
                "searchTerms":[
                    "fa stack overflow"
                ]
            },
            {
                "title":"fa-stackpath",
                "searchTerms":[
                    "fa stackpath"
                ]
            },
            {
                "title":"fa-staff",
                "searchTerms":[
                    "fa staff"
                ]
            },
            {
                "title":"fa-stamp",
                "searchTerms":[
                    "fa stamp"
                ]
            },
            {
                "title":"fa-star",
                "searchTerms":[
                    "fa star"
                ]
            },
            {
                "title":"fa-star-and-crescent",
                "searchTerms":[
                    "fa star and crescent"
                ]
            },
            {
                "title":"fa-star-christmas",
                "searchTerms":[
                    "fa star christmas"
                ]
            },
            {
                "title":"fa-star-exclamation",
                "searchTerms":[
                    "fa star exclamation"
                ]
            },
            {
                "title":"fa-star-half",
                "searchTerms":[
                    "fa star half"
                ]
            },
            {
                "title":"fa-star-half-alt",
                "searchTerms":[
                    "fa star half alt"
                ]
            },
            {
                "title":"fa-star-of-david",
                "searchTerms":[
                    "fa star of david"
                ]
            },
            {
                "title":"fa-star-of-life",
                "searchTerms":[
                    "fa star of life"
                ]
            },
            {
                "title":"fa-stars",
                "searchTerms":[
                    "fa stars"
                ]
            },
            {
                "title":"fa-staylinked",
                "searchTerms":[
                    "fa staylinked"
                ]
            },
            {
                "title":"fa-steak",
                "searchTerms":[
                    "fa steak"
                ]
            },
            {
                "title":"fa-steam",
                "searchTerms":[
                    "fa steam"
                ]
            },
            {
                "title":"fa-steam-square",
                "searchTerms":[
                    "fa steam square"
                ]
            },
            {
                "title":"fa-steam-symbol",
                "searchTerms":[
                    "fa steam symbol"
                ]
            },
            {
                "title":"fa-steering-wheel",
                "searchTerms":[
                    "fa steering wheel"
                ]
            },
            {
                "title":"fa-step-backward",
                "searchTerms":[
                    "fa step backward"
                ]
            },
            {
                "title":"fa-step-forward",
                "searchTerms":[
                    "fa step forward"
                ]
            },
            {
                "title":"fa-stethoscope",
                "searchTerms":[
                    "fa stethoscope"
                ]
            },
            {
                "title":"fa-sticker-mule",
                "searchTerms":[
                    "fa sticker mule"
                ]
            },
            {
                "title":"fa-sticky-note",
                "searchTerms":[
                    "fa sticky note"
                ]
            },
            {
                "title":"fa-stocking",
                "searchTerms":[
                    "fa stocking"
                ]
            },
            {
                "title":"fa-stomach",
                "searchTerms":[
                    "fa stomach"
                ]
            },
            {
                "title":"fa-stop",
                "searchTerms":[
                    "fa stop"
                ]
            },
            {
                "title":"fa-stop-circle",
                "searchTerms":[
                    "fa stop circle"
                ]
            },
            {
                "title":"fa-stopwatch",
                "searchTerms":[
                    "fa stopwatch"
                ]
            },
            {
                "title":"fa-store",
                "searchTerms":[
                    "fa store"
                ]
            },
            {
                "title":"fa-store-alt",
                "searchTerms":[
                    "fa store alt"
                ]
            },
            {
                "title":"fa-strava",
                "searchTerms":[
                    "fa strava"
                ]
            },
            {
                "title":"fa-stream",
                "searchTerms":[
                    "fa stream"
                ]
            },
            {
                "title":"fa-street-view",
                "searchTerms":[
                    "fa street view"
                ]
            },
            {
                "title":"fa-stretcher",
                "searchTerms":[
                    "fa stretcher"
                ]
            },
            {
                "title":"fa-strikethrough",
                "searchTerms":[
                    "fa strikethrough"
                ]
            },
            {
                "title":"fa-stripe",
                "searchTerms":[
                    "fa stripe"
                ]
            },
            {
                "title":"fa-stripe-s",
                "searchTerms":[
                    "fa stripe s"
                ]
            },
            {
                "title":"fa-stroopwafel",
                "searchTerms":[
                    "fa stroopwafel"
                ]
            },
            {
                "title":"fa-studiovinari",
                "searchTerms":[
                    "fa studiovinari"
                ]
            },
            {
                "title":"fa-stumbleupon",
                "searchTerms":[
                    "fa stumbleupon"
                ]
            },
            {
                "title":"fa-stumbleupon-circle",
                "searchTerms":[
                    "fa stumbleupon circle"
                ]
            },
            {
                "title":"fa-subscript",
                "searchTerms":[
                    "fa subscript"
                ]
            },
            {
                "title":"fa-subway",
                "searchTerms":[
                    "fa subway"
                ]
            },
            {
                "title":"fa-suitcase",
                "searchTerms":[
                    "fa suitcase"
                ]
            },
            {
                "title":"fa-suitcase-rolling",
                "searchTerms":[
                    "fa suitcase rolling"
                ]
            },
            {
                "title":"fa-sun",
                "searchTerms":[
                    "fa sun"
                ]
            },
            {
                "title":"fa-sun-cloud",
                "searchTerms":[
                    "fa sun cloud"
                ]
            },
            {
                "title":"fa-sun-dust",
                "searchTerms":[
                    "fa sun dust"
                ]
            },
            {
                "title":"fa-sun-haze",
                "searchTerms":[
                    "fa sun haze"
                ]
            },
            {
                "title":"fa-sunglasses",
                "searchTerms":[
                    "fa sunglasses"
                ]
            },
            {
                "title":"fa-sunrise",
                "searchTerms":[
                    "fa sunrise"
                ]
            },
            {
                "title":"fa-sunset",
                "searchTerms":[
                    "fa sunset"
                ]
            },
            {
                "title":"fa-superpowers",
                "searchTerms":[
                    "fa superpowers"
                ]
            },
            {
                "title":"fa-superscript",
                "searchTerms":[
                    "fa superscript"
                ]
            },
            {
                "title":"fa-supple",
                "searchTerms":[
                    "fa supple"
                ]
            },
            {
                "title":"fa-surprise",
                "searchTerms":[
                    "fa surprise"
                ]
            },
            {
                "title":"fa-suse",
                "searchTerms":[
                    "fa suse"
                ]
            },
            {
                "title":"fa-swatchbook",
                "searchTerms":[
                    "fa swatchbook"
                ]
            },
            {
                "title":"fa-swift",
                "searchTerms":[
                    "fa swift"
                ]
            },
            {
                "title":"fa-swimmer",
                "searchTerms":[
                    "fa swimmer"
                ]
            },
            {
                "title":"fa-swimming-pool",
                "searchTerms":[
                    "fa swimming pool"
                ]
            },
            {
                "title":"fa-sword",
                "searchTerms":[
                    "fa sword"
                ]
            },
            {
                "title":"fa-swords",
                "searchTerms":[
                    "fa swords"
                ]
            },
            {
                "title":"fa-symfony",
                "searchTerms":[
                    "fa symfony"
                ]
            },
            {
                "title":"fa-synagogue",
                "searchTerms":[
                    "fa synagogue"
                ]
            },
            {
                "title":"fa-sync",
                "searchTerms":[
                    "fa sync"
                ]
            },
            {
                "title":"fa-sync-alt",
                "searchTerms":[
                    "fa sync alt"
                ]
            },
            {
                "title":"fa-syringe",
                "searchTerms":[
                    "fa syringe"
                ]
            },
            {
                "title":"fa-table",
                "searchTerms":[
                    "fa table"
                ]
            },
            {
                "title":"fa-table-tennis",
                "searchTerms":[
                    "fa table tennis"
                ]
            },
            {
                "title":"fa-tablet",
                "searchTerms":[
                    "fa tablet"
                ]
            },
            {
                "title":"fa-tablet-alt",
                "searchTerms":[
                    "fa tablet alt"
                ]
            },
            {
                "title":"fa-tablet-android",
                "searchTerms":[
                    "fa tablet android"
                ]
            },
            {
                "title":"fa-tablet-android-alt",
                "searchTerms":[
                    "fa tablet android alt"
                ]
            },
            {
                "title":"fa-tablet-rugged",
                "searchTerms":[
                    "fa tablet rugged"
                ]
            },
            {
                "title":"fa-tablets",
                "searchTerms":[
                    "fa tablets"
                ]
            },
            {
                "title":"fa-tachometer",
                "searchTerms":[
                    "fa tachometer"
                ]
            },
            {
                "title":"fa-tachometer-alt",
                "searchTerms":[
                    "fa tachometer alt"
                ]
            },
            {
                "title":"fa-tachometer-alt-average",
                "searchTerms":[
                    "fa tachometer alt average"
                ]
            },
            {
                "title":"fa-tachometer-alt-fast",
                "searchTerms":[
                    "fa tachometer alt fast"
                ]
            },
            {
                "title":"fa-tachometer-alt-fastest",
                "searchTerms":[
                    "fa tachometer alt fastest"
                ]
            },
            {
                "title":"fa-tachometer-alt-slow",
                "searchTerms":[
                    "fa tachometer alt slow"
                ]
            },
            {
                "title":"fa-tachometer-alt-slowest",
                "searchTerms":[
                    "fa tachometer alt slowest"
                ]
            },
            {
                "title":"fa-tachometer-average",
                "searchTerms":[
                    "fa tachometer average"
                ]
            },
            {
                "title":"fa-tachometer-fast",
                "searchTerms":[
                    "fa tachometer fast"
                ]
            },
            {
                "title":"fa-tachometer-fastest",
                "searchTerms":[
                    "fa tachometer fastest"
                ]
            },
            {
                "title":"fa-tachometer-slow",
                "searchTerms":[
                    "fa tachometer slow"
                ]
            },
            {
                "title":"fa-tachometer-slowest",
                "searchTerms":[
                    "fa tachometer slowest"
                ]
            },
            {
                "title":"fa-taco",
                "searchTerms":[
                    "fa taco"
                ]
            },
            {
                "title":"fa-tag",
                "searchTerms":[
                    "fa tag"
                ]
            },
            {
                "title":"fa-tags",
                "searchTerms":[
                    "fa tags"
                ]
            },
            {
                "title":"fa-tally",
                "searchTerms":[
                    "fa tally"
                ]
            },
            {
                "title":"fa-tanakh",
                "searchTerms":[
                    "fa tanakh"
                ]
            },
            {
                "title":"fa-tape",
                "searchTerms":[
                    "fa tape"
                ]
            },
            {
                "title":"fa-tasks",
                "searchTerms":[
                    "fa tasks"
                ]
            },
            {
                "title":"fa-tasks-alt",
                "searchTerms":[
                    "fa tasks alt"
                ]
            },
            {
                "title":"fa-taxi",
                "searchTerms":[
                    "fa taxi"
                ]
            },
            {
                "title":"fa-teamspeak",
                "searchTerms":[
                    "fa teamspeak"
                ]
            },
            {
                "title":"fa-teeth",
                "searchTerms":[
                    "fa teeth"
                ]
            },
            {
                "title":"fa-teeth-open",
                "searchTerms":[
                    "fa teeth open"
                ]
            },
            {
                "title":"fa-telegram",
                "searchTerms":[
                    "fa telegram"
                ]
            },
            {
                "title":"fa-telegram-plane",
                "searchTerms":[
                    "fa telegram plane"
                ]
            },
            {
                "title":"fa-temperature-frigid",
                "searchTerms":[
                    "fa temperature frigid"
                ]
            },
            {
                "title":"fa-temperature-high",
                "searchTerms":[
                    "fa temperature high"
                ]
            },
            {
                "title":"fa-temperature-hot",
                "searchTerms":[
                    "fa temperature hot"
                ]
            },
            {
                "title":"fa-temperature-low",
                "searchTerms":[
                    "fa temperature low"
                ]
            },
            {
                "title":"fa-tencent-weibo",
                "searchTerms":[
                    "fa tencent weibo"
                ]
            },
            {
                "title":"fa-tenge",
                "searchTerms":[
                    "fa tenge"
                ]
            },
            {
                "title":"fa-tennis-ball",
                "searchTerms":[
                    "fa tennis ball"
                ]
            },
            {
                "title":"fa-terminal",
                "searchTerms":[
                    "fa terminal"
                ]
            },
            {
                "title":"fa-text",
                "searchTerms":[
                    "fa text"
                ]
            },
            {
                "title":"fa-text-height",
                "searchTerms":[
                    "fa text height"
                ]
            },
            {
                "title":"fa-text-size",
                "searchTerms":[
                    "fa text size"
                ]
            },
            {
                "title":"fa-text-width",
                "searchTerms":[
                    "fa text width"
                ]
            },
            {
                "title":"fa-th",
                "searchTerms":[
                    "fa th"
                ]
            },
            {
                "title":"fa-th-large",
                "searchTerms":[
                    "fa th large"
                ]
            },
            {
                "title":"fa-th-list",
                "searchTerms":[
                    "fa th list"
                ]
            },
            {
                "title":"fa-the-red-yeti",
                "searchTerms":[
                    "fa the red yeti"
                ]
            },
            {
                "title":"fa-theater-masks",
                "searchTerms":[
                    "fa theater masks"
                ]
            },
            {
                "title":"fa-themeco",
                "searchTerms":[
                    "fa themeco"
                ]
            },
            {
                "title":"fa-themeisle",
                "searchTerms":[
                    "fa themeisle"
                ]
            },
            {
                "title":"fa-thermometer",
                "searchTerms":[
                    "fa thermometer"
                ]
            },
            {
                "title":"fa-thermometer-empty",
                "searchTerms":[
                    "fa thermometer empty"
                ]
            },
            {
                "title":"fa-thermometer-full",
                "searchTerms":[
                    "fa thermometer full"
                ]
            },
            {
                "title":"fa-thermometer-half",
                "searchTerms":[
                    "fa thermometer half"
                ]
            },
            {
                "title":"fa-thermometer-quarter",
                "searchTerms":[
                    "fa thermometer quarter"
                ]
            },
            {
                "title":"fa-thermometer-three-quarters",
                "searchTerms":[
                    "fa thermometer three quarters"
                ]
            },
            {
                "title":"fa-theta",
                "searchTerms":[
                    "fa theta"
                ]
            },
            {
                "title":"fa-think-peaks",
                "searchTerms":[
                    "fa think peaks"
                ]
            },
            {
                "title":"fa-thumbs-down",
                "searchTerms":[
                    "fa thumbs down"
                ]
            },
            {
                "title":"fa-thumbs-up",
                "searchTerms":[
                    "fa thumbs up"
                ]
            },
            {
                "title":"fa-thumbtack",
                "searchTerms":[
                    "fa thumbtack"
                ]
            },
            {
                "title":"fa-thunderstorm",
                "searchTerms":[
                    "fa thunderstorm"
                ]
            },
            {
                "title":"fa-thunderstorm-moon",
                "searchTerms":[
                    "fa thunderstorm moon"
                ]
            },
            {
                "title":"fa-thunderstorm-sun",
                "searchTerms":[
                    "fa thunderstorm sun"
                ]
            },
            {
                "title":"fa-ticket",
                "searchTerms":[
                    "fa ticket"
                ]
            },
            {
                "title":"fa-ticket-alt",
                "searchTerms":[
                    "fa ticket alt"
                ]
            },
            {
                "title":"fa-tilde",
                "searchTerms":[
                    "fa tilde"
                ]
            },
            {
                "title":"fa-times",
                "searchTerms":[
                    "fa times"
                ]
            },
            {
                "title":"fa-times-circle",
                "searchTerms":[
                    "fa times circle"
                ]
            },
            {
                "title":"fa-times-hexagon",
                "searchTerms":[
                    "fa times hexagon"
                ]
            },
            {
                "title":"fa-times-octagon",
                "searchTerms":[
                    "fa times octagon"
                ]
            },
            {
                "title":"fa-times-square",
                "searchTerms":[
                    "fa times square"
                ]
            },
            {
                "title":"fa-tint",
                "searchTerms":[
                    "fa tint"
                ]
            },
            {
                "title":"fa-tint-slash",
                "searchTerms":[
                    "fa tint slash"
                ]
            },
            {
                "title":"fa-tire",
                "searchTerms":[
                    "fa tire"
                ]
            },
            {
                "title":"fa-tire-flat",
                "searchTerms":[
                    "fa tire flat"
                ]
            },
            {
                "title":"fa-tire-pressure-warning",
                "searchTerms":[
                    "fa tire pressure warning"
                ]
            },
            {
                "title":"fa-tire-rugged",
                "searchTerms":[
                    "fa tire rugged"
                ]
            },
            {
                "title":"fa-tired",
                "searchTerms":[
                    "fa tired"
                ]
            },
            {
                "title":"fa-toggle-off",
                "searchTerms":[
                    "fa toggle off"
                ]
            },
            {
                "title":"fa-toggle-on",
                "searchTerms":[
                    "fa toggle on"
                ]
            },
            {
                "title":"fa-toilet",
                "searchTerms":[
                    "fa toilet"
                ]
            },
            {
                "title":"fa-toilet-paper",
                "searchTerms":[
                    "fa toilet paper"
                ]
            },
            {
                "title":"fa-toilet-paper-alt",
                "searchTerms":[
                    "fa toilet paper alt"
                ]
            },
            {
                "title":"fa-tombstone",
                "searchTerms":[
                    "fa tombstone"
                ]
            },
            {
                "title":"fa-tombstone-alt",
                "searchTerms":[
                    "fa tombstone alt"
                ]
            },
            {
                "title":"fa-toolbox",
                "searchTerms":[
                    "fa toolbox"
                ]
            },
            {
                "title":"fa-tools",
                "searchTerms":[
                    "fa tools"
                ]
            },
            {
                "title":"fa-tooth",
                "searchTerms":[
                    "fa tooth"
                ]
            },
            {
                "title":"fa-toothbrush",
                "searchTerms":[
                    "fa toothbrush"
                ]
            },
            {
                "title":"fa-torah",
                "searchTerms":[
                    "fa torah"
                ]
            },
            {
                "title":"fa-torii-gate",
                "searchTerms":[
                    "fa torii gate"
                ]
            },
            {
                "title":"fa-tornado",
                "searchTerms":[
                    "fa tornado"
                ]
            },
            {
                "title":"fa-tractor",
                "searchTerms":[
                    "fa tractor"
                ]
            },
            {
                "title":"fa-trade-federation",
                "searchTerms":[
                    "fa trade federation"
                ]
            },
            {
                "title":"fa-trademark",
                "searchTerms":[
                    "fa trademark"
                ]
            },
            {
                "title":"fa-traffic-cone",
                "searchTerms":[
                    "fa traffic cone"
                ]
            },
            {
                "title":"fa-traffic-light",
                "searchTerms":[
                    "fa traffic light"
                ]
            },
            {
                "title":"fa-traffic-light-go",
                "searchTerms":[
                    "fa traffic light go"
                ]
            },
            {
                "title":"fa-traffic-light-slow",
                "searchTerms":[
                    "fa traffic light slow"
                ]
            },
            {
                "title":"fa-traffic-light-stop",
                "searchTerms":[
                    "fa traffic light stop"
                ]
            },
            {
                "title":"fa-train",
                "searchTerms":[
                    "fa train"
                ]
            },
            {
                "title":"fa-tram",
                "searchTerms":[
                    "fa tram"
                ]
            },
            {
                "title":"fa-transgender",
                "searchTerms":[
                    "fa transgender"
                ]
            },
            {
                "title":"fa-transgender-alt",
                "searchTerms":[
                    "fa transgender alt"
                ]
            },
            {
                "title":"fa-trash",
                "searchTerms":[
                    "fa trash"
                ]
            },
            {
                "title":"fa-trash-alt",
                "searchTerms":[
                    "fa trash alt"
                ]
            },
            {
                "title":"fa-trash-restore",
                "searchTerms":[
                    "fa trash restore"
                ]
            },
            {
                "title":"fa-trash-restore-alt",
                "searchTerms":[
                    "fa trash restore alt"
                ]
            },
            {
                "title":"fa-trash-undo",
                "searchTerms":[
                    "fa trash undo"
                ]
            },
            {
                "title":"fa-trash-undo-alt",
                "searchTerms":[
                    "fa trash undo alt"
                ]
            },
            {
                "title":"fa-treasure-chest",
                "searchTerms":[
                    "fa treasure chest"
                ]
            },
            {
                "title":"fa-tree",
                "searchTerms":[
                    "fa tree"
                ]
            },
            {
                "title":"fa-tree-alt",
                "searchTerms":[
                    "fa tree alt"
                ]
            },
            {
                "title":"fa-tree-christmas",
                "searchTerms":[
                    "fa tree christmas"
                ]
            },
            {
                "title":"fa-tree-decorated",
                "searchTerms":[
                    "fa tree decorated"
                ]
            },
            {
                "title":"fa-tree-large",
                "searchTerms":[
                    "fa tree large"
                ]
            },
            {
                "title":"fa-tree-palm",
                "searchTerms":[
                    "fa tree palm"
                ]
            },
            {
                "title":"fa-trees",
                "searchTerms":[
                    "fa trees"
                ]
            },
            {
                "title":"fa-trello",
                "searchTerms":[
                    "fa trello"
                ]
            },
            {
                "title":"fa-triangle",
                "searchTerms":[
                    "fa triangle"
                ]
            },
            {
                "title":"fa-triangle-music",
                "searchTerms":[
                    "fa triangle music"
                ]
            },
            {
                "title":"fa-tripadvisor",
                "searchTerms":[
                    "fa tripadvisor"
                ]
            },
            {
                "title":"fa-trophy",
                "searchTerms":[
                    "fa trophy"
                ]
            },
            {
                "title":"fa-trophy-alt",
                "searchTerms":[
                    "fa trophy alt"
                ]
            },
            {
                "title":"fa-truck",
                "searchTerms":[
                    "fa truck"
                ]
            },
            {
                "title":"fa-truck-container",
                "searchTerms":[
                    "fa truck container"
                ]
            },
            {
                "title":"fa-truck-couch",
                "searchTerms":[
                    "fa truck couch"
                ]
            },
            {
                "title":"fa-truck-loading",
                "searchTerms":[
                    "fa truck loading"
                ]
            },
            {
                "title":"fa-truck-monster",
                "searchTerms":[
                    "fa truck monster"
                ]
            },
            {
                "title":"fa-truck-moving",
                "searchTerms":[
                    "fa truck moving"
                ]
            },
            {
                "title":"fa-truck-pickup",
                "searchTerms":[
                    "fa truck pickup"
                ]
            },
            {
                "title":"fa-truck-plow",
                "searchTerms":[
                    "fa truck plow"
                ]
            },
            {
                "title":"fa-truck-ramp",
                "searchTerms":[
                    "fa truck ramp"
                ]
            },
            {
                "title":"fa-trumpet",
                "searchTerms":[
                    "fa trumpet"
                ]
            },
            {
                "title":"fa-tshirt",
                "searchTerms":[
                    "fa tshirt"
                ]
            },
            {
                "title":"fa-tty",
                "searchTerms":[
                    "fa tty"
                ]
            },
            {
                "title":"fa-tumblr",
                "searchTerms":[
                    "fa tumblr"
                ]
            },
            {
                "title":"fa-tumblr-square",
                "searchTerms":[
                    "fa tumblr square"
                ]
            },
            {
                "title":"fa-turkey",
                "searchTerms":[
                    "fa turkey"
                ]
            },
            {
                "title":"fa-turntable",
                "searchTerms":[
                    "fa turntable"
                ]
            },
            {
                "title":"fa-turtle",
                "searchTerms":[
                    "fa turtle"
                ]
            },
            {
                "title":"fa-tv",
                "searchTerms":[
                    "fa tv"
                ]
            },
            {
                "title":"fa-tv-alt",
                "searchTerms":[
                    "fa tv alt"
                ]
            },
            {
                "title":"fa-tv-music",
                "searchTerms":[
                    "fa tv music"
                ]
            },
            {
                "title":"fa-tv-retro",
                "searchTerms":[
                    "fa tv retro"
                ]
            },
            {
                "title":"fa-twitch",
                "searchTerms":[
                    "fa twitch"
                ]
            },
            {
                "title":"fa-twitter",
                "searchTerms":[
                    "fa twitter"
                ]
            },
            {
                "title":"fa-twitter-square",
                "searchTerms":[
                    "fa twitter square"
                ]
            },
            {
                "title":"fa-typewriter",
                "searchTerms":[
                    "fa typewriter"
                ]
            },
            {
                "title":"fa-typo3",
                "searchTerms":[
                    "fa typo3"
                ]
            },
            {
                "title":"fa-uber",
                "searchTerms":[
                    "fa uber"
                ]
            },
            {
                "title":"fa-ubuntu",
                "searchTerms":[
                    "fa ubuntu"
                ]
            },
            {
                "title":"fa-uikit",
                "searchTerms":[
                    "fa uikit"
                ]
            },
            {
                "title":"fa-umbraco",
                "searchTerms":[
                    "fa umbraco"
                ]
            },
            {
                "title":"fa-umbrella",
                "searchTerms":[
                    "fa umbrella"
                ]
            },
            {
                "title":"fa-umbrella-beach",
                "searchTerms":[
                    "fa umbrella beach"
                ]
            },
            {
                "title":"fa-underline",
                "searchTerms":[
                    "fa underline"
                ]
            },
            {
                "title":"fa-undo",
                "searchTerms":[
                    "fa undo"
                ]
            },
            {
                "title":"fa-undo-alt",
                "searchTerms":[
                    "fa undo alt"
                ]
            },
            {
                "title":"fa-unicorn",
                "searchTerms":[
                    "fa unicorn"
                ]
            },
            {
                "title":"fa-union",
                "searchTerms":[
                    "fa union"
                ]
            },
            {
                "title":"fa-uniregistry",
                "searchTerms":[
                    "fa uniregistry"
                ]
            },
            {
                "title":"fa-universal-access",
                "searchTerms":[
                    "fa universal access"
                ]
            },
            {
                "title":"fa-university",
                "searchTerms":[
                    "fa university"
                ]
            },
            {
                "title":"fa-unlink",
                "searchTerms":[
                    "fa unlink"
                ]
            },
            {
                "title":"fa-unlock",
                "searchTerms":[
                    "fa unlock"
                ]
            },
            {
                "title":"fa-unlock-alt",
                "searchTerms":[
                    "fa unlock alt"
                ]
            },
            {
                "title":"fa-untappd",
                "searchTerms":[
                    "fa untappd"
                ]
            },
            {
                "title":"fa-upload",
                "searchTerms":[
                    "fa upload"
                ]
            },
            {
                "title":"fa-ups",
                "searchTerms":[
                    "fa ups"
                ]
            },
            {
                "title":"fa-usb",
                "searchTerms":[
                    "fa usb"
                ]
            },
            {
                "title":"fa-usb-drive",
                "searchTerms":[
                    "fa usb drive"
                ]
            },
            {
                "title":"fa-usd-circle",
                "searchTerms":[
                    "fa usd circle"
                ]
            },
            {
                "title":"fa-usd-square",
                "searchTerms":[
                    "fa usd square"
                ]
            },
            {
                "title":"fa-user",
                "searchTerms":[
                    "fa user"
                ]
            },
            {
                "title":"fa-user-alt",
                "searchTerms":[
                    "fa user alt"
                ]
            },
            {
                "title":"fa-user-alt-slash",
                "searchTerms":[
                    "fa user alt slash"
                ]
            },
            {
                "title":"fa-user-astronaut",
                "searchTerms":[
                    "fa user astronaut"
                ]
            },
            {
                "title":"fa-user-chart",
                "searchTerms":[
                    "fa user chart"
                ]
            },
            {
                "title":"fa-user-check",
                "searchTerms":[
                    "fa user check"
                ]
            },
            {
                "title":"fa-user-circle",
                "searchTerms":[
                    "fa user circle"
                ]
            },
            {
                "title":"fa-user-clock",
                "searchTerms":[
                    "fa user clock"
                ]
            },
            {
                "title":"fa-user-cog",
                "searchTerms":[
                    "fa user cog"
                ]
            },
            {
                "title":"fa-user-cowboy",
                "searchTerms":[
                    "fa user cowboy"
                ]
            },
            {
                "title":"fa-user-crown",
                "searchTerms":[
                    "fa user crown"
                ]
            },
            {
                "title":"fa-user-edit",
                "searchTerms":[
                    "fa user edit"
                ]
            },
            {
                "title":"fa-user-friends",
                "searchTerms":[
                    "fa user friends"
                ]
            },
            {
                "title":"fa-user-graduate",
                "searchTerms":[
                    "fa user graduate"
                ]
            },
            {
                "title":"fa-user-hard-hat",
                "searchTerms":[
                    "fa user hard hat"
                ]
            },
            {
                "title":"fa-user-headset",
                "searchTerms":[
                    "fa user headset"
                ]
            },
            {
                "title":"fa-user-injured",
                "searchTerms":[
                    "fa user injured"
                ]
            },
            {
                "title":"fa-user-lock",
                "searchTerms":[
                    "fa user lock"
                ]
            },
            {
                "title":"fa-user-md",
                "searchTerms":[
                    "fa user md"
                ]
            },
            {
                "title":"fa-user-md-chat",
                "searchTerms":[
                    "fa user md chat"
                ]
            },
            {
                "title":"fa-user-minus",
                "searchTerms":[
                    "fa user minus"
                ]
            },
            {
                "title":"fa-user-music",
                "searchTerms":[
                    "fa user music"
                ]
            },
            {
                "title":"fa-user-ninja",
                "searchTerms":[
                    "fa user ninja"
                ]
            },
            {
                "title":"fa-user-nurse",
                "searchTerms":[
                    "fa user nurse"
                ]
            },
            {
                "title":"fa-user-plus",
                "searchTerms":[
                    "fa user plus"
                ]
            },
            {
                "title":"fa-user-secret",
                "searchTerms":[
                    "fa user secret"
                ]
            },
            {
                "title":"fa-user-shield",
                "searchTerms":[
                    "fa user shield"
                ]
            },
            {
                "title":"fa-user-slash",
                "searchTerms":[
                    "fa user slash"
                ]
            },
            {
                "title":"fa-user-tag",
                "searchTerms":[
                    "fa user tag"
                ]
            },
            {
                "title":"fa-user-tie",
                "searchTerms":[
                    "fa user tie"
                ]
            },
            {
                "title":"fa-user-times",
                "searchTerms":[
                    "fa user times"
                ]
            },
            {
                "title":"fa-users",
                "searchTerms":[
                    "fa users"
                ]
            },
            {
                "title":"fa-users-class",
                "searchTerms":[
                    "fa users class"
                ]
            },
            {
                "title":"fa-users-cog",
                "searchTerms":[
                    "fa users cog"
                ]
            },
            {
                "title":"fa-users-crown",
                "searchTerms":[
                    "fa users crown"
                ]
            },
            {
                "title":"fa-users-medical",
                "searchTerms":[
                    "fa users medical"
                ]
            },
            {
                "title":"fa-usps",
                "searchTerms":[
                    "fa usps"
                ]
            },
            {
                "title":"fa-ussunnah",
                "searchTerms":[
                    "fa ussunnah"
                ]
            },
            {
                "title":"fa-utensil-fork",
                "searchTerms":[
                    "fa utensil fork"
                ]
            },
            {
                "title":"fa-utensil-knife",
                "searchTerms":[
                    "fa utensil knife"
                ]
            },
            {
                "title":"fa-utensil-spoon",
                "searchTerms":[
                    "fa utensil spoon"
                ]
            },
            {
                "title":"fa-utensils",
                "searchTerms":[
                    "fa utensils"
                ]
            },
            {
                "title":"fa-utensils-alt",
                "searchTerms":[
                    "fa utensils alt"
                ]
            },
            {
                "title":"fa-vaadin",
                "searchTerms":[
                    "fa vaadin"
                ]
            },
            {
                "title":"fa-value-absolute",
                "searchTerms":[
                    "fa value absolute"
                ]
            },
            {
                "title":"fa-vector-square",
                "searchTerms":[
                    "fa vector square"
                ]
            },
            {
                "title":"fa-venus",
                "searchTerms":[
                    "fa venus"
                ]
            },
            {
                "title":"fa-venus-double",
                "searchTerms":[
                    "fa venus double"
                ]
            },
            {
                "title":"fa-venus-mars",
                "searchTerms":[
                    "fa venus mars"
                ]
            },
            {
                "title":"fa-vhs",
                "searchTerms":[
                    "fa vhs"
                ]
            },
            {
                "title":"fa-viacoin",
                "searchTerms":[
                    "fa viacoin"
                ]
            },
            {
                "title":"fa-viadeo",
                "searchTerms":[
                    "fa viadeo"
                ]
            },
            {
                "title":"fa-viadeo-square",
                "searchTerms":[
                    "fa viadeo square"
                ]
            },
            {
                "title":"fa-vial",
                "searchTerms":[
                    "fa vial"
                ]
            },
            {
                "title":"fa-vials",
                "searchTerms":[
                    "fa vials"
                ]
            },
            {
                "title":"fa-viber",
                "searchTerms":[
                    "fa viber"
                ]
            },
            {
                "title":"fa-video",
                "searchTerms":[
                    "fa video"
                ]
            },
            {
                "title":"fa-video-plus",
                "searchTerms":[
                    "fa video plus"
                ]
            },
            {
                "title":"fa-video-slash",
                "searchTerms":[
                    "fa video slash"
                ]
            },
            {
                "title":"fa-vihara",
                "searchTerms":[
                    "fa vihara"
                ]
            },
            {
                "title":"fa-vimeo",
                "searchTerms":[
                    "fa vimeo"
                ]
            },
            {
                "title":"fa-vimeo-square",
                "searchTerms":[
                    "fa vimeo square"
                ]
            },
            {
                "title":"fa-vimeo-v",
                "searchTerms":[
                    "fa vimeo v"
                ]
            },
            {
                "title":"fa-vine",
                "searchTerms":[
                    "fa vine"
                ]
            },
            {
                "title":"fa-violin",
                "searchTerms":[
                    "fa violin"
                ]
            },
            {
                "title":"fa-vk",
                "searchTerms":[
                    "fa vk"
                ]
            },
            {
                "title":"fa-vnv",
                "searchTerms":[
                    "fa vnv"
                ]
            },
            {
                "title":"fa-voicemail",
                "searchTerms":[
                    "fa voicemail"
                ]
            },
            {
                "title":"fa-volcano",
                "searchTerms":[
                    "fa volcano"
                ]
            },
            {
                "title":"fa-volleyball-ball",
                "searchTerms":[
                    "fa volleyball ball"
                ]
            },
            {
                "title":"fa-volume",
                "searchTerms":[
                    "fa volume"
                ]
            },
            {
                "title":"fa-volume-down",
                "searchTerms":[
                    "fa volume down"
                ]
            },
            {
                "title":"fa-volume-mute",
                "searchTerms":[
                    "fa volume mute"
                ]
            },
            {
                "title":"fa-volume-off",
                "searchTerms":[
                    "fa volume off"
                ]
            },
            {
                "title":"fa-volume-slash",
                "searchTerms":[
                    "fa volume slash"
                ]
            },
            {
                "title":"fa-volume-up",
                "searchTerms":[
                    "fa volume up"
                ]
            },
            {
                "title":"fa-vote-nay",
                "searchTerms":[
                    "fa vote nay"
                ]
            },
            {
                "title":"fa-vote-yea",
                "searchTerms":[
                    "fa vote yea"
                ]
            },
            {
                "title":"fa-vr-cardboard",
                "searchTerms":[
                    "fa vr cardboard"
                ]
            },
            {
                "title":"fa-vuejs",
                "searchTerms":[
                    "fa vuejs"
                ]
            },
            {
                "title":"fa-wagon-covered",
                "searchTerms":[
                    "fa wagon covered"
                ]
            },
            {
                "title":"fa-walker",
                "searchTerms":[
                    "fa walker"
                ]
            },
            {
                "title":"fa-walkie-talkie",
                "searchTerms":[
                    "fa walkie talkie"
                ]
            },
            {
                "title":"fa-walking",
                "searchTerms":[
                    "fa walking"
                ]
            },
            {
                "title":"fa-wallet",
                "searchTerms":[
                    "fa wallet"
                ]
            },
            {
                "title":"fa-wand",
                "searchTerms":[
                    "fa wand"
                ]
            },
            {
                "title":"fa-wand-magic",
                "searchTerms":[
                    "fa wand magic"
                ]
            },
            {
                "title":"fa-warehouse",
                "searchTerms":[
                    "fa warehouse"
                ]
            },
            {
                "title":"fa-warehouse-alt",
                "searchTerms":[
                    "fa warehouse alt"
                ]
            },
            {
                "title":"fa-washer",
                "searchTerms":[
                    "fa washer"
                ]
            },
            {
                "title":"fa-watch",
                "searchTerms":[
                    "fa watch"
                ]
            },
            {
                "title":"fa-watch-calculator",
                "searchTerms":[
                    "fa watch calculator"
                ]
            },
            {
                "title":"fa-watch-fitness",
                "searchTerms":[
                    "fa watch fitness"
                ]
            },
            {
                "title":"fa-water",
                "searchTerms":[
                    "fa water"
                ]
            },
            {
                "title":"fa-water-lower",
                "searchTerms":[
                    "fa water lower"
                ]
            },
            {
                "title":"fa-water-rise",
                "searchTerms":[
                    "fa water rise"
                ]
            },
            {
                "title":"fa-wave-sine",
                "searchTerms":[
                    "fa wave sine"
                ]
            },
            {
                "title":"fa-wave-square",
                "searchTerms":[
                    "fa wave square"
                ]
            },
            {
                "title":"fa-wave-triangle",
                "searchTerms":[
                    "fa wave triangle"
                ]
            },
            {
                "title":"fa-waveform",
                "searchTerms":[
                    "fa waveform"
                ]
            },
            {
                "title":"fa-waveform-path",
                "searchTerms":[
                    "fa waveform path"
                ]
            },
            {
                "title":"fa-waze",
                "searchTerms":[
                    "fa waze"
                ]
            },
            {
                "title":"fa-webcam",
                "searchTerms":[
                    "fa webcam"
                ]
            },
            {
                "title":"fa-webcam-slash",
                "searchTerms":[
                    "fa webcam slash"
                ]
            },
            {
                "title":"fa-weebly",
                "searchTerms":[
                    "fa weebly"
                ]
            },
            {
                "title":"fa-weibo",
                "searchTerms":[
                    "fa weibo"
                ]
            },
            {
                "title":"fa-weight",
                "searchTerms":[
                    "fa weight"
                ]
            },
            {
                "title":"fa-weight-hanging",
                "searchTerms":[
                    "fa weight hanging"
                ]
            },
            {
                "title":"fa-weixin",
                "searchTerms":[
                    "fa weixin"
                ]
            },
            {
                "title":"fa-whale",
                "searchTerms":[
                    "fa whale"
                ]
            },
            {
                "title":"fa-whatsapp",
                "searchTerms":[
                    "fa whatsapp"
                ]
            },
            {
                "title":"fa-whatsapp-square",
                "searchTerms":[
                    "fa whatsapp square"
                ]
            },
            {
                "title":"fa-wheat",
                "searchTerms":[
                    "fa wheat"
                ]
            },
            {
                "title":"fa-wheelchair",
                "searchTerms":[
                    "fa wheelchair"
                ]
            },
            {
                "title":"fa-whistle",
                "searchTerms":[
                    "fa whistle"
                ]
            },
            {
                "title":"fa-whmcs",
                "searchTerms":[
                    "fa whmcs"
                ]
            },
            {
                "title":"fa-wifi",
                "searchTerms":[
                    "fa wifi"
                ]
            },
            {
                "title":"fa-wifi-1",
                "searchTerms":[
                    "fa wifi 1"
                ]
            },
            {
                "title":"fa-wifi-2",
                "searchTerms":[
                    "fa wifi 2"
                ]
            },
            {
                "title":"fa-wifi-slash",
                "searchTerms":[
                    "fa wifi slash"
                ]
            },
            {
                "title":"fa-wikipedia-w",
                "searchTerms":[
                    "fa wikipedia w"
                ]
            },
            {
                "title":"fa-wind",
                "searchTerms":[
                    "fa wind"
                ]
            },
            {
                "title":"fa-wind-turbine",
                "searchTerms":[
                    "fa wind turbine"
                ]
            },
            {
                "title":"fa-wind-warning",
                "searchTerms":[
                    "fa wind warning"
                ]
            },
            {
                "title":"fa-window",
                "searchTerms":[
                    "fa window"
                ]
            },
            {
                "title":"fa-window-alt",
                "searchTerms":[
                    "fa window alt"
                ]
            },
            {
                "title":"fa-window-close",
                "searchTerms":[
                    "fa window close"
                ]
            },
            {
                "title":"fa-window-maximize",
                "searchTerms":[
                    "fa window maximize"
                ]
            },
            {
                "title":"fa-window-minimize",
                "searchTerms":[
                    "fa window minimize"
                ]
            },
            {
                "title":"fa-window-restore",
                "searchTerms":[
                    "fa window restore"
                ]
            },
            {
                "title":"fa-windows",
                "searchTerms":[
                    "fa windows"
                ]
            },
            {
                "title":"fa-windsock",
                "searchTerms":[
                    "fa windsock"
                ]
            },
            {
                "title":"fa-wine-bottle",
                "searchTerms":[
                    "fa wine bottle"
                ]
            },
            {
                "title":"fa-wine-glass",
                "searchTerms":[
                    "fa wine glass"
                ]
            },
            {
                "title":"fa-wine-glass-alt",
                "searchTerms":[
                    "fa wine glass alt"
                ]
            },
            {
                "title":"fa-wix",
                "searchTerms":[
                    "fa wix"
                ]
            },
            {
                "title":"fa-wizards-of-the-coast",
                "searchTerms":[
                    "fa wizards of the coast"
                ]
            },
            {
                "title":"fa-wolf-pack-battalion",
                "searchTerms":[
                    "fa wolf pack battalion"
                ]
            },
            {
                "title":"fa-won-sign",
                "searchTerms":[
                    "fa won sign"
                ]
            },
            {
                "title":"fa-wordpress",
                "searchTerms":[
                    "fa wordpress"
                ]
            },
            {
                "title":"fa-wordpress-simple",
                "searchTerms":[
                    "fa wordpress simple"
                ]
            },
            {
                "title":"fa-wpbeginner",
                "searchTerms":[
                    "fa wpbeginner"
                ]
            },
            {
                "title":"fa-wpexplorer",
                "searchTerms":[
                    "fa wpexplorer"
                ]
            },
            {
                "title":"fa-wpforms",
                "searchTerms":[
                    "fa wpforms"
                ]
            },
            {
                "title":"fa-wpressr",
                "searchTerms":[
                    "fa wpressr"
                ]
            },
            {
                "title":"fa-wreath",
                "searchTerms":[
                    "fa wreath"
                ]
            },
            {
                "title":"fa-wrench",
                "searchTerms":[
                    "fa wrench"
                ]
            },
            {
                "title":"fa-x-ray",
                "searchTerms":[
                    "fa x ray"
                ]
            },
            {
                "title":"fa-xbox",
                "searchTerms":[
                    "fa xbox"
                ]
            },
            {
                "title":"fa-xing",
                "searchTerms":[
                    "fa xing"
                ]
            },
            {
                "title":"fa-xing-square",
                "searchTerms":[
                    "fa xing square"
                ]
            },
            {
                "title":"fa-y-combinator",
                "searchTerms":[
                    "fa y combinator"
                ]
            },
            {
                "title":"fa-yahoo",
                "searchTerms":[
                    "fa yahoo"
                ]
            },
            {
                "title":"fa-yammer",
                "searchTerms":[
                    "fa yammer"
                ]
            },
            {
                "title":"fa-yandex",
                "searchTerms":[
                    "fa yandex"
                ]
            },
            {
                "title":"fa-yandex-international",
                "searchTerms":[
                    "fa yandex international"
                ]
            },
            {
                "title":"fa-yarn",
                "searchTerms":[
                    "fa yarn"
                ]
            },
            {
                "title":"fa-yelp",
                "searchTerms":[
                    "fa yelp"
                ]
            },
            {
                "title":"fa-yen-sign",
                "searchTerms":[
                    "fa yen sign"
                ]
            },
            {
                "title":"fa-yin-yang",
                "searchTerms":[
                    "fa yin yang"
                ]
            },
            {
                "title":"fa-yoast",
                "searchTerms":[
                    "fa yoast"
                ]
            },
            {
                "title":"fa-youtube",
                "searchTerms":[
                    "fa youtube"
                ]
            },
            {
                "title":"fa-youtube-square",
                "searchTerms":[
                    "fa youtube square"
                ]
            },
            {
                "title":"fa-zhihu",
                "searchTerms":[
                    "fa zhihu"
                ]
            }
        ]
    });
});
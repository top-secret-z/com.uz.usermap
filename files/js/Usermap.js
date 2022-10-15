"use strict";

/**
 * Location-related classes for Usermap
 * 
 * @author        2014-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.usermap
 * 
 * In parts imple copy of WCF.Location.js
 * 
 * Location-related classes for WCF
 * 
 * @author    Matthias Schmidt
 * @copyright    2001-2018 WoltLab GmbH
 * @license    GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
var Usermap = { };
Usermap.User = { };

/**
 * Appends latitude/longitude to form parameters on submit.
 * 
 * @param    WCF.Location.GoogleMaps.LocationInput        locationInput
 */
Usermap.User.CoordinatesHandler = Class.extend({
    /**
     * form element
     * @var    jQuery
     */
    _form: null,

    /**
     * location input object
     * @var    WCF.Location.GoogleMaps.LocationInput
     */
    _locationInput: null,

    /**
     * Initializes the class.
     */
    init: function(locationInput) {
        this._locationInput = locationInput;

        this._form = $('#messageContainer').submit($.proxy(this._submit, this));
    },

    /**
     * Handles the submit event.
     */
    _submit: function(event) {
        if (this._form.data('geocodingCompleted')) {
            return true;
        }

        var $location = $.trim($('#geocode').val());
        if (!$location) {
            WCF.Location.GoogleMaps.Util.reverseGeocoding($.proxy(this._reverseGeocoding, this), this._locationInput.getMarker());

            event.preventDefault();
            return false;
        }

        this._setCoordinates();
    },

    /**
     * Performs a reverse geocoding request.
     */
    _reverseGeocoding: function(location) {
        $('#geocode').val(location);

        this._setCoordinates();
        this._form.trigger('submit');
    },

    /**
     * Appends the coordinates to form parameters.
     */
    _setCoordinates: function() {
        var $formSubmit = this._form.find('.formSubmit');
        $('<input type="hidden" name="latitude" value="' + this._locationInput.getMarker().getPosition().lat() + '" />').appendTo($formSubmit);
        $('<input type="hidden" name="longitude" value="' + this._locationInput.getMarker().getPosition().lng() + '" />').appendTo($formSubmit);

        this._form.data('geocodingCompleted', true);
    }
});

Usermap.Map = { };
Usermap.Map.GoogleMaps = { };

/**
 * Handles the global Google Maps settings for usermap.
 */
Usermap.Map.GoogleMaps.Settings = {
    /**
     * Google Maps settings
     */
    _settings: { },

    /**
     * Returns the value of a certain setting or null if it doesn't exist.
     */
    get: function(setting) {
        if (setting === undefined) {
            return this._settings;
        }

        if (this._settings[setting] !== undefined) {
            return this._settings[setting];
        }

        return null;
    },

    /**
     * Sets the value of a certain setting.
     */
    set: function(setting, value) {
        if ($.isPlainObject(setting)) {
            for (var index in setting) {
                this._settings[index] = setting[index];
            }
        }
        else {
            this._settings[setting] = value;
        }
    }
};

/**
 * Handles a Google Maps map.
 */
Usermap.Map.GoogleMaps.Map = Class.extend({
    /**
     * map object for the displayed map
     */
    _map: null,

    /**
     * list of markers on the map
     */
    _markers: [ ],

    /**
     * list of infoWindows on the map
     */
    _infoWindows: [ ],

    /**
     * Initalizes a new WCF.Location.Map object.
     */
    init: function(mapContainerID, mapOptions) {
        this._mapContainer = $('#' + mapContainerID);
        this._mapOptions = $.extend(true, this._getDefaultMapOptions(), mapOptions);

        this._map = new google.maps.Map(this._mapContainer[0], this._mapOptions);
        this._markers = [ ];
        this._infoWindows = [ ];

        // fix maps in mobile sidebars by refreshing the map when displaying the map
        if (this._mapContainer.parents('.sidebar').length) {
            enquire.register('(max-width: 767px)', {
                setup: $.proxy(this._addSidebarMapListener, this),
                deferSetup: true
            });
        }

        this.refresh();
    },

    /**
     * Adds the event listener to a marker to show the associated info window.
     */
    _addInfoWindowEventListener: function(marker, infoWindow) {
        google.maps.event.addListener(marker, 'click', $.proxy(function() {
            infoWindow.open(this._map, marker);
        }, this));
    },

    /**
     * Adds click listener to mobile sidebar toggle button to refresh map.
     */
    _addSidebarMapListener: function() {
        $('.content > .mobileSidebarToggleButton').click($.proxy(this.refresh, this));
    },

    /**
     * Returns the default map options.
     * 
     * @return    object
     */
    _getDefaultMapOptions: function() {
        var $defaultMapOptions = { };

        // dummy center value
        $defaultMapOptions.center = new google.maps.LatLng(Usermap.Map.GoogleMaps.Settings.get('defaultLatitude'), Usermap.Map.GoogleMaps.Settings.get('defaultLongitude'));

        // double click to zoom
        $defaultMapOptions.disableDoubleClickZoom = Usermap.Map.GoogleMaps.Settings.get('disableDoubleClickZoom');

        // draggable
        $defaultMapOptions.draggable = Usermap.Map.GoogleMaps.Settings.get('draggable');

        // map type
        switch (Usermap.Map.GoogleMaps.Settings.get('mapType')) {
            case 'map':
                $defaultMapOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
            break;

            case 'satellite':
                $defaultMapOptions.mapTypeId = google.maps.MapTypeId.SATELLITE;
            break;

            case 'physical':
                $defaultMapOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
            break;

            case 'hybrid':
            default:
                $defaultMapOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
            break;
        }

        /// map type controls
        $defaultMapOptions.mapTypeControl = Usermap.Map.GoogleMaps.Settings.get('mapTypeControl') != 'off';
        if ($defaultMapOptions.mapTypeControl) {
            switch (Usermap.Map.GoogleMaps.Settings.get('mapTypeControl')) {
                case 'dropdown':
                    $defaultMapOptions.mapTypeControlOptions = {
                        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                    };
                break;

                case 'horizontalBar':
                    $defaultMapOptions.mapTypeControlOptions = {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                    };
                break;

                default:
                    $defaultMapOptions.mapTypeControlOptions = {
                        style: google.maps.MapTypeControlStyle.DEFAULT
                    };
                break;
            }
        }

        // scale control
        $defaultMapOptions.scaleControl = Usermap.Map.GoogleMaps.Settings.get('scaleControl');
        $defaultMapOptions.scrollwheel = Usermap.Map.GoogleMaps.Settings.get('scrollwheel');

        // zoom
        $defaultMapOptions.zoom = Usermap.Map.GoogleMaps.Settings.get('zoom');

        return $defaultMapOptions;
    },

    /**
     * Adds a draggable marker at the given position to the map and returns the created marker object.
     */
    addDraggableMarker: function(latitude, longitude) {
        var $marker = new google.maps.Marker({
            clickable: false,
            draggable: true,
            map: this._map,
            position: new google.maps.LatLng(latitude, longitude),
            zIndex: 1
        });

        this._markers.push($marker);

        return $marker;
    },

    /**
     * Adds a marker with the given data to the map and returns the created marker object.
     */
    addMarker: function(latitude, longitude, title, icon, information) {
        var $marker = new google.maps.Marker({
            map: this._map,
            position: new google.maps.LatLng(latitude, longitude),
            title: title
        });

        // add icon
        if (icon) {
            $marker.setIcon(icon);
        }

        // add info window for marker information
        if (information) {
            var $infoWindow = new google.maps.InfoWindow({
                content: information
            });
            this._addInfoWindowEventListener($marker, $infoWindow);

            // add info window object to marker object
            $marker.infoWindow = $infoWindow;

            // store
            this._infoWindows.push($infoWindow);
        }

        this._markers.push($marker);

        return $marker;
    },

    /**
     * Returns all markers on the map.
     */
    getMarkers: function() {
        return this._markers;
    },

    /**
     * Returns the Google Maps map object.
     */
    getMap: function() {
        return this._map;
    },

    /**
     * Refreshes the map.
     */
    refresh: function() {
        // save current center since resize does not preserve it
        var $center = this._map.getCenter();

        google.maps.event.trigger(this._map, 'resize');

        // set center to old value again
        this._map.setCenter($center);
    },

    /**
     * Refreshes the boundaries of the map to show all markers.
     */
    refreshBounds: function() {
        var $minLatitude = null;
        var $maxLatitude = null;
        var $minLongitude = null;
        var $maxLongitude = null;

        for (var $index in this._markers) {
            var $marker = this._markers[$index];
            var $latitude = $marker.getPosition().lat();
            var $longitude = $marker.getPosition().lng();

            if ($minLatitude === null) {
                $minLatitude = $maxLatitude = $latitude;
                $minLongitude = $maxLongitude = $longitude;
            }
            else {
                if ($minLatitude > $latitude) {
                    $minLatitude = $latitude;
                }
                else if ($maxLatitude < $latitude) {
                    $maxLatitude = $latitude;
                }

                if ($minLongitude > $latitude) {
                    $minLongitude = $latitude;
                }
                else if ($maxLongitude < $longitude) {
                    $maxLongitude = $longitude;
                }
            }
        }

        this._map.fitBounds(new google.maps.LatLngBounds(
            new google.maps.LatLng($minLatitude, $minLongitude),
            new google.maps.LatLng($maxLatitude, $maxLongitude)
        ));
    },

    /**
     * Removes all markers from the map.
     */
    removeMarkers: function() {
        for (var $index in this._markers) {
            this._markers[$index].setMap(null);
        }

        this._markers = [ ];
    },

    /**
     * Changes the bounds of the map.
     */
    setBounds: function(northEast, southWest) {
        this._map.fitBounds(new google.maps.LatLngBounds(
            new google.maps.LatLng(southWest.latitude, southWest.longitude),
            new google.maps.LatLng(northEast.latitude, northEast.longitude)
        ));
    },

    /**
     * Sets the center of the map to the given position.
     */
    setCenter: function(latitude, longitude) {
        this._map.setCenter(new google.maps.LatLng(latitude, longitude));
    }
});

/**
 * Handles a large map with many markers where (new) markers are loaded via AJAX.
 */
Usermap.Map.LargeMap = Usermap.Map.GoogleMaps.Map.extend({
    /**
     * additional parameters for various switches
     */
    _additionalParameters: { },

    /**
     * indicates if the maps center can be set by location search
     */
    _locationSearch: null,

    /**
     * selector for the location search input
     */
    _locationSearchInputSelector: null,

    /**
     * cluster handling the markers on the map
     */
    _markerClusterer: null,

    /**
     * Input field for user search and radius
     */
    _userSearchInput: null,

    /**
     * switch for inital loading of markers to avoid unneccessary reloads
     */
    _markersLoaded: false,

    /**
     * map bounds after loading of markers
     */
    _bounds: null,


    /**
     * Circles
     */
    _circleUser: null,
    _circleLocation: null,

    /**
     * Buttons
     */
    _buttonCenter: null,
    _buttonCleanup: null,
    _buttonFilter: null,
    _buttonResetFilter: null,

    /**
     * Filter
     */
    _filterOnline: 0,
    _filterFollower: 0,
    _filterTeam: 0,
    _count: 0,

    _loadingOverlay: null,

    /**
     * Selected groups
     */
    _selectedGroups: [ ],

    /**
     * Saved markers / open markers
     */
    _markerInfoSave: [ ],
    _markerOpen: [ ],

    /**
     * data for direction service
     */
    _directionsService: null,
    _directionsDisplay: null,

    /**
     * search markers
     */
    _searchMarker: [],

    /**
     * @see    WCF.Location.GoogleMaps.Map.init()
     */
    init: function(mapContainerID, mapOptions, locationSearchInputSelector, additionalParameters) {
        this._super(mapContainerID, mapOptions);

        this._additionalParameters = additionalParameters || { };

        // preset, define and disable some buttons
        this._count = 0;
        this._buttonCenter = $('#centerButton');
        this._buttonCleanup = $('#cleanupButton');
        this._buttonFilter = $('#filterButton');
        this._buttonResetFilter = $('#filterResetButton');
        this._buttonCenter.disable();
        this._buttonCleanup.disable();
        this._buttonFilter.disable();
        this._buttonResetFilter.disable();

        this._locationSearchInputSelector = locationSearchInputSelector || '';
        if (this._locationSearchInputSelector) {
            this._locationSearch = new WCF.Location.GoogleMaps.LocationSearch(locationSearchInputSelector, $.proxy(this._locationList, this));
        }

        this._markerClusterer = new MarkerClusterer(this._map, this._markers, {
            maxZoom: 17,
            imagePath: Usermap.Map.GoogleMaps.Settings.get('markerClustererImagePath') + 'm'
        });

        this._markerSpiderfier = new OverlappingMarkerSpiderfier(this._map, {
            keepSpiderfied: true,
            markersWontHide: true,
            markersWontMove: true
        });
        this._markerSpiderfier.addListener('click', $.proxy(function(marker) {
            if (marker.infoWindow) {
                if (marker.infoWindow.getMap()) {
                    marker.infoWindow.close();

                    // memorize open markers
                    var index = this._markerOpen.indexOf(marker);
                    if (index > -1) {
                        this._markerOpen.splice(index, 1);
                    }
                }
                else {
                    marker.infoWindow.open(this._map, marker);

                    // memorize open markers
                    this._markerOpen.push(marker);
                }
            }
        }, this));

        this._proxy = new WCF.Action.Proxy({
            showLoadingOverlay: true,
            success: $.proxy(this._success, this)
        });

        google.maps.event.addListener(this._map, 'idle', $.proxy(this._loadMarkers, this));

        // init own stuff
        this._filterOnline = 0;
        this._filterFollower = 0;
        this._filterTeam = 0;

        // location / user search / route
        this._userSearchInput = $('#userSearchInput');
        $('#searchButton').click($.proxy(this._search, this));
        $('#routeButton').click($.proxy(this._route, this));

        // center and cleanup button
        this._buttonCenter = $('#centerButton');
        this._buttonCenter.click($.proxy(this._centerBounds, this));
        this._buttonCleanup = $('#cleanupButton');
        this._buttonCleanup.click($.proxy(this._cleanup, this));

        // filter
        this._selectedGroups.push(0);
        this._buttonFilter = $('#filterButton');
        this._buttonFilter.click($.proxy(this._filter, this));
        this._buttonResetFilter = $('#filterResetButton');
        this._buttonResetFilter.click($.proxy(this._filterReset, this));
        this._buttonResetFilter.hide();
    },

    /**
     * Handles click on filter reset button.
     */
    _filterReset: function() {
        // reset filters
        this._filterOnline = 0;
        this._filterFollower = 0;
        this._filterTeam = 0;

        // user filter
        if (this._additionalParameters.userFilter) {
            document.getElementById('onlineFilter').checked = false;
            document.getElementById('followerFilter').checked = false;
            document.getElementById('teamFilter').checked = false;
        }

        // group filter
        if (this._additionalParameters.groupFilter) {
            var $groupCheckBoxes = document.getElementsByName('usermapGroup');
            if ($groupCheckBoxes.length) {
                for (var $i = 0; $i < $groupCheckBoxes.length; $i++) {
                    $groupCheckBoxes[$i].checked = true;
                }
            }
        }

        // filter new
        this._filter();

        // reset filter text and button
        document.getElementById('filterActive').innerHTML = '';
        this._buttonResetFilter.hide();
    },

    /**
     * Handles click on filter button.
     */
    _filter: function() {
        // group filter
        if (this._additionalParameters.groupFilter) {
            var $groupCheckBoxes = document.getElementsByName('usermapGroup');
            var $groups = [];
            for (var $i = 0; $i < $groupCheckBoxes.length; $i++) {
                if ($groupCheckBoxes[$i].checked == true) {
                    $groups.push(parseInt($groupCheckBoxes[$i].value));
                }
            }
        }

        // filter online / follower
        this._filterOnline = 0;
        this._filterFollower = 0;
        this._filterTeam = 0;
        if (this._additionalParameters.userFilter) {
            var $temp = document.getElementById('onlineFilter');
            if ($temp.checked == true) { this._filterOnline = 1; }
            $temp = document.getElementById('followerFilter');
            if ($temp.checked == true) { this._filterFollower = 1; }
            $temp = document.getElementById('teamFilter');
            if ($temp.checked == true) { this._filterTeam = 1; }
        }

        // get new marker 
        var $temp = [ ];
        var $count = 0;

        for (var $i = 0; $i < this._markerInfoSave.length; $i++) {
            // user filter
            if (this._additionalParameters.userFilter) {
                if (this._filterOnline && this._markerInfoSave[$i].online != 1) { continue }
                if (this._filterFollower && this._markerInfoSave[$i].follower != 1) { continue }
                if (this._filterTeam && this._markerInfoSave[$i].team != 1) { continue }
            }

            // group filter
            if (this._additionalParameters.groupFilter) {
                if ($groupCheckBoxes.length) {
                    var $userGroups = Object.keys(this._markerInfoSave[$i].groups);
                    var $index = -1;
                    for (var $k = 0; $k < $userGroups.length; $k++) {
                        var $groupID = parseInt($userGroups[$k]);
                        $index = $groups.indexOf($groupID);
                        if ($index > -1) { break; }
                    }
                    if ($index < 0) { continue; }
                }
                else { continue; }
            }

            $temp.push(this._markerInfoSave[$i]);
            $count ++;
        }

        // remove markers
        this._markerClusterer.clearMarkers();
        this._markerSpiderfier.clearMarkers();

        // load markers if any
        this._bounds = null;
        this._bounds = new google.maps.LatLngBounds();

        for (var $i = 0; $i < $temp.length; $i++) {
            var $markerInfo = $temp[$i];

            this.addMarker($markerInfo.latitude, $markerInfo.longitude, $markerInfo.title, $markerInfo.icon, $markerInfo.infoWindow, $markerInfo.dialog, $markerInfo.location);

            // get bounds from all loaded markers
            this._bounds.extend(new google.maps.LatLng($markerInfo.latitude, $markerInfo.longitude));
        }

        // set info to active, show button
        document.getElementById('filterActive').innerHTML = WCF.Language.get('usermap.user.map.filter.active');
        this._buttonResetFilter.show();
    },

    /**
     * Handles clicking on the cleanup button.
     */
    _cleanup: function() {
        // close circles
        if (this._circleUser) {
            this._circleUser.setMap(null);
            this._circleUser = null;
        }
        if (this._circleLocation) {
            this._circleLocation.setMap(null);
            this._circleLocation = null;
        }

        // remove search pins
        if (this._searchMarker.length) {
            for (var $i = 0, $length = this._searchMarker.length; $i < $length; $i++) {
                this._searchMarker[$i].setMap(null);
            }
            this._searchMarker = [ ];
        }

        // close info windows by filtering again
        if (this._markerOpen.length) {
            this._markerOpen = [ ];
            this._filter();
        }

        // remove direction
        if (this._directionsDisplay !== null) {
            this._directionsDisplay.setMap(null);
            this._directionsDisplay = null;
        }
        if (this._directionsService !== null) {
            this._directionsService = null;
        }
    },

    /**
     * Handles clicking on the route button.
     */
    _route: function() {
        // remove existing
        if (this._directionsDisplay !== null) {
            this._directionsDisplay.setMap(null);
            this._directionsDisplay = null;
        }
        if (this._directionsService !== null) {
            this._directionsService = null;
        }

        // abort if no sufficient points
        if (this._searchMarker.length + this._markerOpen.length < 2) {
            $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.route.error') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
        }
        else {
            // search results are start, end and then way points
            // open markers are way points if search results
            var $start = null;
            var $end = null;
            var $limit = 0;
            var $count = 0;
            var $temp = 0;
            var $tempArray = [ ];
            var $wayPoints = [ ];

            if (this._searchMarker.length) {
                $limit = 0;
                while($count < 25 && $limit < this._searchMarker.length) {
                    $temp = this._searchMarker[$limit].getPosition();
                    $wayPoints.push({
                        location: $temp,
                        stopover: true
                    });
                    $count ++;
                    $limit ++;
                }
            }
            if (this._markerOpen.length) {
                $limit = 0;
                while($count < 25 && $limit < this._markerOpen.length) {
                    $temp = this._markerOpen[$limit].getPosition();
                    $wayPoints.push({
                        location: $temp,
                        stopover: true
                    });
                    $count ++;
                    $limit ++;
                }
            }

            if ($wayPoints.length > 1) {
                $start = $wayPoints[0].location;
                $end = $wayPoints[1].location;
                $wayPoints.splice(0, 2);
            }

            // get directions
            this._directionsService = new google.maps.DirectionsService();
            this._directionsDisplay = new google.maps.DirectionsRenderer({
                suppressMarkers: true
            });
            this._directionsDisplay.setMap(this.getMap());
            var $directionsDisplay = this._directionsDisplay;

            var $request = {
                    origin:         $start,
                    destination:     $end,
                    waypoints:        $wayPoints,
                    travelMode:     'DRIVING'
            };

            this._directionsService.route($request, function(response, status) {
                if (status == 'OK') {
                    $directionsDisplay.setDirections(response);

                // display dialog with route information
                var $legs = response.routes[0].legs;
                var points = response.geocoded_waypoints.length;
                var $waypoints = [ ];
                var $distance = 0
                var $wayString = '';

                var $text = '<div><p>' + WCF.Language.get('usermap.user.map.route.found') + '</p><br>';

                for (var $i = 0, $length = $legs.length; $i < $length; $i++) {
                    if ($waypoints.indexOf($legs[$i].start_address) < 0) {
                        $waypoints.push($legs[$i].start_address);
                        $wayString += encodeURIComponent($legs[$i].start_address) + '/';
                    }
                    if ($waypoints.indexOf($legs[$i].end_address) < 0) {
                        $waypoints.push($legs[$i].end_address);
                        $wayString += encodeURIComponent($legs[$i].end_address) + '/';
                    }

                    $distance += $legs[$i].distance.value;

                    $text = $text.concat('<p>' + $legs[$i].start_address + '</p>');
                    $text = $text.concat('<p>' + $legs[$i].end_address + '</p>');
                    $text = $text.concat('<p>' + $legs[$i].distance.text + '</p><br>');
                }

                $text = $text.concat('<p>' + WCF.Language.get('usermap.user.map.route.waypoints') + ' ' + points + '</p>');
                $text = $text.concat('<p>' + WCF.Language.get('usermap.user.map.route.distance') + ' ' + parseInt($distance / 1000) + '</p>');

                $text = $text.concat('<div class="formSubmit"><a href="https://www.google.com/maps/dir/' + $wayString + ' "{if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if}{if EXTERNAL_LINK_REL_NOFOLLOW} rel="nofollow"{/if}" class="button" > <span>' + WCF.Language.get('usermap.user.map.route.open') + '</span></a></div>');

                $('<div>' + $text + '</div>').wcfDialog({ title: WCF.Language.get('usermap.user.map.route')});
                }
                else {
                    $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.search.error.direction') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
                }
            });
        }
    },

    /**
     * Handles clicking on the center button.
     */
    _centerBounds: function() {
        // center iaw configuration
        if (Usermap.Map.GoogleMaps.Settings.get('centerOnOpen')) {
            if (this._bounds) {
                this.getMap().fitBounds(this._bounds);
            }
        }
        else {
            this._map.setCenter(new google.maps.LatLng(Usermap.Map.GoogleMaps.Settings.get('defaultLatitude'), Usermap.Map.GoogleMaps.Settings.get('defaultLongitude')));
            this._map.setZoom(Usermap.Map.GoogleMaps.Settings.get('zoom'));
        }
    },

    /**
     * Handles clicking on the search button.
     */
    _search: function() {
        var $username = document.getElementById('userSearchInput').value;
        var $location = document.getElementById('geocode').value;

        if (!$username && !$location) {
            $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.search.error.bothEmpty') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
        }
        else {
            //    this._userResetButton.show();

            var $proxy = new WCF.Action.Proxy({
                autoSend: true,
                showLoadingOverlay: true,
                data: {
                    actionName: 'search',
                    className: 'usermap\\data\\usermap\\UsermapAction',
                    parameters: {
                        username: $username,
                        location: $location
                    },
                },
                success: $.proxy(this._searchSuccess, this)
            });
        }
    },

    /**
     * handles search result
     */
    _searchSuccess: function(data, textStatus, jqXHR) {
        var $username = document.getElementById('userSearchInput').value;
        var $location = document.getElementById('geocode').value;
        var $data = data.returnValues;

        if ($username && $location) {
            if (typeof $data.userLat === 'undefined' && typeof $data.locationLat == 'undefined') {
                $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.search.error.bothNotFound') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
            }
            else if (typeof $data.userLat === 'undefined') {
                $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.search.error.userNotFound') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
            }
            else if (typeof $data.locationLat == 'undefined') {
                $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.search.error.locationNotFound') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
            }
        }
        else if ($username) {
            if (typeof $data.userLat === 'undefined') {
                $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.search.error.userNotFound') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
            }
        }
        else {
            if (typeof $data.locationLat === 'undefined') {
                $('<header class="boxHeadline">' + WCF.Language.get('usermap.user.map.search.error.locationNotFound') + '</header>').wcfDialog({ title: WCF.Language.get('wcf.global.error.title') });
            }
        }

        // display found (one or both)
        this._bounds = new google.maps.LatLngBounds();
        var $userFound = 0;
        var $locationFound = 0;

        // user
        if ($username && typeof $data.userLat !== 'undefined') {
            if (this._circleUser) {
                this._circleUser.setMap(null);
                this._circleUser = null;
            }
            this._circleUser = new google.maps.Circle({
                strokeColor: '#008000',
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: '#008000',
                fillOpacity: 0.1,
                map: this._map,
                center: new google.maps.LatLng($data.userLat, $data.userLng),
                radius: 2500,
                editable: false,
                visible: true
            });
            this._bounds.extend(new google.maps.LatLng($data.userLat, $data.userLng));
            $userFound = 1;

            // new marker?
            var latLng = {lat: $data.userLat, lng: $data.userLng};
            var $found = 0;
            if (this._searchMarker.length) {
                for (var $i = 0, $length = this._searchMarker.length; $i < $length; $i++) {
                    var lat = this._searchMarker[$i].getPosition().lat();
                    var lng = this._searchMarker[$i].getPosition().lng();
                    lat = lat.toFixed(7);
                    lng = lng.toFixed(7);

                    if (lat == latLng.lat && lng == latLng.lng) {
                        $found = 1;
                        break;
                    }
                }
            }
            if ($found == 0) {
                var marker = new google.maps.Marker({
                    position:     new google.maps.LatLng(latLng.lat, latLng.lng),
                    map:         this._map,
                    icon:        $data.icon,
                    title:         $username,
                    zIndex:        999999
                });
                marker.setMap(this._map);
                this._searchMarker.push(marker);
            }
        }

        if ($location && typeof $data.locationLat !== 'undefined') {
            if (this._circleLocation) {
                this._circleLocation.setMap(null);
                this._circleLocation = null;
            }
            this._circleLocation = new google.maps.Circle({
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: '#FF0000',
                fillOpacity: 0.1,
                map: this._map,
                center: new google.maps.LatLng($data.locationLat, $data.locationLng),
                radius: 2500,
                editable: false,
                visible: true
            });
            this._bounds.extend(new google.maps.LatLng($data.locationLat, $data.locationLng));
            $locationFound = 1;

            var latLng = {lat: $data.locationLat, lng: $data.locationLng};
            var $found = 0;
            if (this._searchMarker.length) {
                for (var $i = 0, $length = this._searchMarker.length; $i < $length; $i++) {
                    var lat = this._searchMarker[$i].getPosition().lat();
                    var lng = this._searchMarker[$i].getPosition().lng();
                    lat = lat.toFixed(7);
                    lng = lng.toFixed(7);

                    if (lat == latLng.lat && lng == latLng.lng) {
                        $found = 1;
                        break;
                    }
                }
            }
            if ($found == 0) {
                var marker = new google.maps.Marker({
                    position:     new google.maps.LatLng(latLng.lat, latLng.lng),
                    map:         this._map,
                    icon:        $data.icon,
                    title:         $location,
                    zIndex:        999999
                });
                marker.setMap(this._map);
                this._searchMarker.push(marker);
            }
        }

        if ($userFound && $locationFound) {
            this.getMap().fitBounds(this._bounds);
        }
        else if ($userFound) {
            this._map.fitBounds(this._circleUser.getBounds());
        }
        else {
            this._map.fitBounds(this._circleLocation.getBounds());
        }
    },

    /**
     * @see    WCF.Location.GoogleMaps.Map.addMarker()
     */
    _addInfoWindowEventListener: function(marker, infoWindow) {

    },

    /**
     * Fills location input based on a location search result.
     * 
     * @param    object        data
     */
    _locationList: function(data) {
        $(this._locationSearchInputSelector).val(data.label);
    },

    /**
     * Loads markers only once for fitted bounds for all
     */
    _loadMarkers: function() {
        // already loaded?
        if (this._markersLoaded === false) {
            this._proxy.setOption('data', {
                className: 'usermap\\data\\usermap\\UsermapAction',
                actionName: 'getMapMarkers'
            });

            this._proxy.sendRequest();
            this._markersLoaded = true;
            return true;
        }
        return false;
    },

    /**
     * Handles a successful AJAX request.
     */
    _success: function(data, textStatus, jqXHR) {
        // save bounds in any case
        this._bounds = new google.maps.LatLngBounds();

        if (data.returnValues && data.returnValues.markers) {
            for (var $i = 0, $length = data.returnValues.markers.length; $i < $length; $i++) {
                var $markerInfo = data.returnValues.markers[$i];

                this.addMarker($markerInfo.latitude, $markerInfo.longitude, $markerInfo.title, $markerInfo.icon, $markerInfo.infoWindow, $markerInfo.dialog, $markerInfo.location);

                // save marker info
                this._markerInfoSave.push($markerInfo);
                this._count ++;

                // get bounds from all loaded markers
                this._bounds.extend(new google.maps.LatLng($markerInfo.latitude, $markerInfo.longitude));
            }
        }

        // center map if configured
        if ($length && Usermap.Map.GoogleMaps.Settings.get('centerOnOpen')) {
            this.getMap().fitBounds(this._bounds);
        }

        // show disabled buttons
        this._buttonCenter.enable();
        this._buttonCleanup.enable();
        this._buttonFilter.enable();
        this._buttonResetFilter.enable();
    },

    /**
     * @see    WCF.Location.GoogleMaps.Map.addMarker()
     */
    addMarker: function(latitude, longitude, title, icon, information, dialog, location) {
        var $information = $(information).get(0);
        var $marker = this._super(latitude, longitude, title, icon, $information);

        this._markerClusterer.addMarker($marker);
        this._markerSpiderfier.addMarker($marker);

        if (dialog) {
            // skip, want to scroll in infoWindow / remove if staying with spiderfyer
        }

        return $marker.infoWindow;
    }
});

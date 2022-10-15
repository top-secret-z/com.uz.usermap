Pictures say more than 1000 words. This is certainly true for the place of residence or the favorite places of your members. Instead of looking up the member's profile, simply let yourself and members of your community show a Google Maps map displaying your members' places. With what? Of course, with  **User Map**. It is not only an optical highlight in your community, but can also create new contact options for your members.

### Description

This application for WoltLab Suite allows members of your community to define a location that is displayed to other users on a Google Maps map and in the user profile. For this purpose, either a custom user field into which the location is entered via the keyboard or a graphical map interface can be used. Depending on the configuration, both can be automatically synchronized with each other.

If you are already using custom user fields for locations, you can synchronize them with the user map using the "Rebuild Data - Rebuild User map" function in the ACP. The data in the existing custom fields are summarized, transferred to the newly installed field "User map", are then converted into coordinates (geocoding) and transferred to the map.

The user map is designed as an application and offers many ways to display users. The location icons on the map (markers) can be set group-specific for the users; you can also use your own markers. The map entries can be filtered according to various criteria, such as online members or followers or user groups. You can search for users and places in the map, and also view routes between selected users or locations. These routes can then be further processed on Google Maps if required.

A click on a user's marker opens a small window in which a link to the user profile and the location of the user are displayed. Opening the marker also defines this location as a point of a possible route. The latter also applies to searched locations / users.

As usual in the WoltLab Suite,  **User Map**  also supplements conditions for various purposes such as user searches, notices or advertisements and adds statistics as well as additional user information in the profile and in the sidebar.

**User Map** has a visible copyright notice. This can be removed with a paid branding-free extension.

### Geocoding

**User Map**  uses geocoding data from Google Maps and OpenStreetMap. Google Maps is used for normal use in the frontend, and data from OpenStreetMap is first used when updating the user map in the ACP. If no OpenStreetMap data can be found, Google is searched for location data. The main reason for this is Google's restrictions on geocoding services; Google currently allows only for 2,500 free daily requests. As a result, user map updating in the ACP can take a long time for communities with many users. And since OpenStreetMap has also introduced limits for geocoding, only one location per second can be processed in the ACP. So please have a little patience when rebuilding the user map data.

Successful geocoding queries on Google or OpenStreetMap are internally stored in a cache and are available for subsequent queries. The time span after which the geocoding cache is updated can be set in the ACP. Additionally, geocoding-relevant user actions are stored in a log, so that in case of problems you may find out where they might come from.

It is also possible to specify an additional Browser API Key for geocoding, which is then used internally for some functions (search, synchronization). This makes it easier to implement restrictions of the primary key used in the system. The additional key should be restricted to the Geocoding API with suitable limits.

### Configuration

In addition to user group permissions for the use of the map, various display options for the user map can be configured in the ACP and, in particular, settings for data synchronization can be made.

As of version 5.2.1, it is possible to hide deactivated, blocked and / or inactive users via options.

### Notices

**User Map**  is an application. WoltLab Suite Core, however, is not included. In addition, customizations (rewrite / .htaccess resp. nginx equivalent) must be made when URL-rewrites (SEO) are activated.

When opening the user map, all user entries are loaded at once. For communities with many users, this takes some time (opening the map with 10,000 user entries on my root server takes up to 10 seconds) and script memory for the entries. The minimum requirements for WoltLab Suite (memory_limit of 128 MB) may not be sufficient for large communities.

In order to use  **User Map**, a Google Maps browser key must be configured in the ACP and the community must be registered for Google Maps. See  [Get a Key / Authentication](https://developers.google.com/maps/documentation/javascript/get-api-key).

Google limits not only the geocoding requests but also the map calls (currently 25,000). For more information, see  [API Usage Limitations](https://developers.google.com/maps/documentation/javascript/usage).
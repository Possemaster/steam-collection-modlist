# steam-collection-modlist
PHP script for generating MOD IDS from a Steam Collection

You may use all files without permission.
If you want to add functionality please commit changes so we can check if we can add it to the main branch.

## Requirements
PHP 7.1 or newer with either the `curl` extension, or `allow_url_fopen` enabled together with the `openssl` extension.
Mod IDs are fetched from the public Steam Web API (`ISteamRemoteStorage/GetCollectionDetails`), no API key needed.

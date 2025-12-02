fx_version 'adamant'
lua54 'yes'
author 'Randale'
description 'Made to Protect and Secure - https://discord.fivesecurity.de'
game 'gta5'
shared_script '@FiveSecurity/server/shared.lua'

client_scripts {
	'client/client.lua',
}

server_scripts {
	'config/Config.lua',
	'server/server.lua',
    'server/updater.lua',
	'server/installer.js',
}

files {
	'hashes.json',
	'html/index.html',
	'html/script.js',
}

shared_script 'server/shared.lua'

ui_page 'html/index.html'
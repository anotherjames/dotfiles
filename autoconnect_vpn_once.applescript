# on idle
tell application "System Events"
	tell current location of network preferences
		set myVPN to the service "VPN NAME HERE"
		if myVPN is not null then
			if current configuration of myVPN is not connected then
				connect myVPN
			end if
		end if
	end tell
	return
end tell
# end idle

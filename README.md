# AAU-Calendar-Fix-Industrial-Design
Fixes the improper use of iCal of the Moodle calendar for Industrial Design Engineering students at Aalborg University. Removes all unnecessary information from titles and places it in the proper field.
If a similar problem occurs at a different department/university this solution might also work for you.

## The problem in short
The events made by the moodle calendar put all the details in the title-field of an event, instead of placing it in the proper field.
For example, all location information is put in the title, while there is a dedicated field for the location of events. This code fixes this to make the calendar overview more useful - only leaving the important information in the title field of the events. All other information is moved to the proper fields. In case there is no dedicated field for the information (such as teacher contact details) they will be placed in the _description_ field.

# Usage
1. Login and export your AAU calendar in Moodle at: (for non-AAU users, hope you can find this yourself ;))
[https://www.moodle.aau.dk/calendar/export.php](https://www.moodle.aau.dk/calendar/export.php)
This will give you an url that looks something like this:
https://www.moodle.aau.dk/calendar/export_execute.php?userid=XXXX&authtoken=XXXXX&preset_what=XXX&preset_time=XXXX

2. Copy the last part of that URL and add it to this url:
http://gerkevangarderen.nl/icalAAU.php
This should look like this:
http://gerkevangarderen.nl/icalAAU.php?userid=XXXX&authtoken=XXXXX&preset_what=XXX&preset_time=XXXX
(XXX's would of course be your personal Moodle content)

3. Use that URL to add in your calendar. It is an ical URL and can be used with most calendar applications (Google Calendar, Apple Calendar, Microsoft Outlook, etc.)

NB: I have tested it, and it fully works for me (Google Calendar). It should work in any other application.
Please do check if nothing is missing in your calendar, as it is a DIY-project and might not be 100% solid.

NB NB: I don't store any data, your login is safe. You can check it in the [Source code](icalAAU.php) here.
Also, the small bits of login information that you do share, can't be used to login to your account. There is no big risk.
If you want to play it 100% safe, download a copy and host it on your own webserver :)

# AAU-Calendar-Fix-Industrial-Design
Fixes the improper use of iCal of the Moodle calendar. Removes all unnecessary information from titles and places it in the proper field.

The events made by the moodle calendar put all the details in the title-field of an event, instead of placing it in the proper field.
For example, all location information is put in the title, while there is a dedicated field for the location of events. This code fixes this to make the calendar overview more useful - only leaving the important information in the title field of the events. All other information is moved to the proper fields. In case there is no dedicated field for the information (such as teacher contact details) they will be placed in the _description_ field.

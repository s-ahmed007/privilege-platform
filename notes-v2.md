# Turn off paid membership

- Hide membership/card related texts from everywhere (web, app, admin)
###Existing users
- Change `customer_type` in `customer_info` table
- Change `expiry_date` to a long period in `customer_info` table
- Do something with the `month` (if needed) (both in customer_info & ssl table)
- Change type in `customer_history` table
###New user
- Make user premium at registration from everywhere (web, app, admin)


##Steps done
- Turned off cron jobs, related to user paid membership
- Command created to change exp & type of every user
- View removed from admin
- Made user premium at registration from the website, app & admin
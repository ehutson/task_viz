# TaskViz

This is a simple utility that can be used to visualize the timing of transporter tasks for a previously sent mailing.

To run, edit `index.php` replacing the values of `[MY_ORACLE_USER]`, `[MY_ORACLE_PASSWORD]`, and `[ORACLE_CONNECTION_STRING]` with the appropriate values for your pod and user.

Then, run `start.sh`.  This will fetch a php container with oracle support and launch a server running on port 9999.

To access the report, go to [http://localhost:9999](http://localhost:9999) and enter the mailing id of the mailing you would like to visualize.


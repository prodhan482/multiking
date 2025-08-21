# File Upload Scheduling on Digital OCean Space.

To upload local files to Digital Ocean Space on Schedule time. 

We will use Python 3 for the time schedule development. Here is the plan.

- Configure a cron tab for every 10 minute to check database table and upload file.
- For any file upload laravel will save a entry to mysql database table
- A python command line scrip will execute and read that table and start uploading file.
- As the file gets uploaded to digitalocean space, it will remove the local file

### Prepare Environment

- Install python 3
- install pip
- Go to root folder of ``digitalOceanSpace_FileUpload`` and run command ``pip install -r requirements.txt``
- Run command ``chmod +x /<web_app_base_path>/digitalOceanSpace_FileUpload/doUpload.py`` to make file executable.
- You can test by run script ``/<web_app_base_path>/digitalOceanSpace_FileUpload/doUpload.py``

### Crontab

``
*/5 * * * *  /<web_app_base_path>/digitalOceanSpace_FileUpload/doUpload.py
``

### Python 3 Libraries

- boto3 (for aws upload)
- pymysql (MySql Table Connection)


### S3cmd for 

#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import boto3
import pymysql
import os
import json
import mimetypes
from dotenv import load_dotenv


def start():
    # Loading Laravel Environment File.

    dotenv_path = os.path.abspath(
        os.path.join(os.path.dirname(__file__), '..', '.env'))
    load_dotenv(dotenv_path)

    connection = pymysql.connect(host=os.environ.get("DB_HOST"),
                                 user=os.environ.get("DB_USERNAME"),
                                 port=int(os.environ.get("DB_PORT")),
                                 password=os.environ.get("DB_PASSWORD"),
                                 database=os.environ.get("DB_DATABASE"),
                                 cursorclass=pymysql.cursors.DictCursor)

    with connection:
        with connection.cursor() as cursor:
            sql = "SELECT * FROM pending_dig_ocn_spc"
            cursor.execute(sql)
            result = cursor.fetchall()

            if not result:
                print("No Pending Upload Found.")

            for data in result:
                upload(data)
    return


def upload(dataDetails):
    if os.path.exists(dataDetails['upload_absolute_path']):
        session = boto3.session.Session()
        client = session.client('s3',
                                region_name='fra1',
                                endpoint_url='https://fra1.digitaloceanspaces.com',
                                aws_access_key_id='DEMEA2QGUCBAAV6PFTTI',
                                aws_secret_access_key='U5ayoPPLaNPHVtZAAHP5CWLk3z2T/lqtWYrOnVcx0eU',
                                config=boto3.session.Config(signature_version='s3v4', retries={
                                    'max_attempts': 10,
                                    'mode': 'standard'
                                }, s3={'addressing_style': "virtual"})
                                )

        mt = "binary/octet-stream"
        mt_get = mimetypes.guess_type(dataDetails['upload_absolute_path'])
        if mt_get:
            mt = mt_get[0]

        client.upload_file(dataDetails['upload_absolute_path'],  # Path to local file
                           'hd22-data',  # Name of Space
                           dataDetails['remote_file_name'],
                           ExtraArgs={'ContentType': mt, 'ACL': 'public-read'})  # Name for remote file

        # Delete Local file.
        if os.path.exists(dataDetails['upload_absolute_path']):
            os.remove(dataDetails['upload_absolute_path'])
        else:
            print("The file does not exist")

        connection = pymysql.connect(host=os.environ.get("DB_HOST"),
                                     user=os.environ.get("DB_USERNAME"),
                                     port=int(os.environ.get("DB_PORT")),
                                     password=os.environ.get("DB_PASSWORD"),
                                     database=os.environ.get("DB_DATABASE"),
                                     cursorclass=pymysql.cursors.DictCursor)


        with connection:
            with connection.cursor() as cursor:
                sql = "DELETE FROM `pending_dig_ocn_spc` WHERE `pending_dig_ocn_spc`.`id` = %s"
                print(sql)
                cursor.execute(sql, (dataDetails['id']))

                column_name = json.loads(dataDetails["column_name"])
                table_primary_key_val = json.loads(dataDetails["table_primary_key_val"])

                logic_q = ""

                for i in range(len(column_name)):
                    if not logic_q:
                        logic_q = logic_q + "`" + dataDetails["table_name"] + "`.`" + column_name[i] + "` = '" + \
                                  table_primary_key_val[i] + "'"
                    else:
                        logic_q = logic_q + " AND `" + dataDetails["table_name"] + "`.`" + column_name[i] + "` = '" + \
                                  table_primary_key_val[i] + "'"

                sql = "UPDATE `" + dataDetails["table_name"] + "` SET `space_uploaded` = 'uploaded' WHERE " + logic_q
                print(sql)
                cursor.execute(sql)
            connection.commit()

        return

"""
INSERT INTO `pending_dig_ocn_spc` (`id`, `upload_absolute_path`, `remote_file_name`, `table_name`, `column_name`, `table_primary_key_val`) VALUES
('627e4c5a3ce38f4d092d5939226cc', '/var/www/public/assets/simcard_offer/simcard_offer_1650817866.jpg', 'assets/simcard_offer/simcard_offer_1650817866.jpg', 'sc_simcard_offer', '[\"id\"]', '[\"627e4c5a29125601299714aefe7b0\"]');
"""

if __name__ == '__main__':
    start()

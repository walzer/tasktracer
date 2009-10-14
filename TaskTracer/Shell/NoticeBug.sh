#!/bin/bash
# File to Mail bugs assigned to somebody of BugFree system.
#
# BugFree is free software under the terms of the FreeBSD License.
#
# @link        http://www.bugfree.org.cn
# @package     BugFree
#
ServerName="http://www.bugfree.org.cn"
php /www/bugfree/Notice.php $ServerName

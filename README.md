# TestCsrf
PHP tool to test Cross Site Request Forgery aka CSRF.  
Note this is an automated tool, manual check still required.  

```
Usage: testcsrf.php [OPTIONS] -o <token> -f <request_file>

Options:
	-h	print this help
	-m	test mode, default all
		0: remove the token
		1: change the value of the token (but keep the length the same)
		2: remove the value of the token (but leave the parameter in place)
		3: convert to a GET request
	-o	token name
	-r	do not follow redirection
	-s	force https
	-t	set tolerance for result output, default=5

Examples:
	testcsrf.php -p "§=10" -f request.txt
	testcsrf.php -s -p "^=bob,alice,jim" -f request.txt
	testcsrf.php -t 10 -s -p "|=5;^=bob,alice,jim;$=123,456,789" -f request.txt
```

I don't believe in license.  
You can do want you want with this program.  

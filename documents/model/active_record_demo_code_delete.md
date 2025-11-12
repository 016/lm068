```php
$user = User::find(10);

if ($user) {
    if ($user->delete()) {
        echo "用户删除成功！";
    } else {
        print_r($user->errors);
    }
}
```
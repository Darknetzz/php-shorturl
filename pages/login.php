<div class="container-fluid d-flex justify-content-center">
    <div class='card m-3 border border-primary'>
        <h1 class='card-header text-bg-primary'>Login</h1>
        <div class='card-body'>
            <form class='dynamic-form' method='POST' data-action='login'>
                <table class='table table-default'>
                    <tr>
                        <td>Username</td>
                        <td>
                            <input class='form-control username' type='text' name='username' placeholder='Username' autofocus>
                        </td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>
                            <input class='form-control password' type='password' name='password' placeholder='Password'>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input class='btn btn-primary' type='submit' name='login' value='Login'>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<{include file ="../Common/header.tpl"}>
<{include file ="../Common/navibar.tpl"}>
<{include file ="../Common/sidebar.tpl"}>

<!--- START 以上内容不需更改，保证该TPL页内的标签匹配即可 --->


	
	<div class="block">
        <a href="#page-stats" class="block-heading" data-toggle="collapse">当前用户信息</a>
        <div id="page-stats" class="block-body collapse in">
			
               <table class="table table-striped">  
						     
							 <tr>
						        <td>用户名</td>
						        <td>真实姓名</td>
						        <td>手机号</td>
						        <td>Email</td>
						        <td>登录时间</td>
						        <td>登录IP</td>
					          </tr>
						      <tr>
						        <td><{$user_info.user_name}></td>
						        <td><{$user_info.real_name}></td>
						        <td><{$user_info.mobile}></td>
						        <td><{$user_info.email}></td>
						        <td><{date("Y-m-d H:i:s",$user_info.login_time)}></td>
						        <td><{$user_info.login_ip}></td>
					          </tr>
					        
					      </table>
		</div>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<strong>注意！</strong>请保管好您的个人信息，一点发生密码泄露请紧急联系管理员。</div>
        </div>
    </div>
	
<!--- END 以下内容不需更改，请保证该TPL页内的标签匹配即可 --->
<{include file="../Common/footer.tpl"}>
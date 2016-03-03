# printTicket
print goods ticket model

#安装
1.把文件解压后部署到web服务器根目录下
2.执行项目根目录下ticket.sql脚本进行初始化数据库结构和数据
3.修改config目录下的数据库配置信息
4.按照下面测试链接地址进行测试

#目录结构说明
config		--系统配置目录
core		--模块核心层
dal			--数据访问层
  -model	--数据模型层
lib			--类库
index.php	--示例文件

#测试
http:域名/index.php?action=test1  #当购买的商品中，有符合“买二赠一”优惠条件的商品时
http:域名/index.php?action=test2  #当购买的商品中，没有符合“买二赠一”优惠条件的商品时
http:域名/index.php?action=test3  #当购买的商品中，有符合“95折”优惠条件的商品时
http:域名/index.php?action=test4  #当购买的商品中，有符合“95折”优惠条件的商品，又有符合“买二赠一”优惠条件的商品时
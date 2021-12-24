#!/bin/bash
LANG="en_US.UTF-8" 
LANG="zh_CN.UTF-8"

echo "\
+-------------------------------------------+
| Duckchat - 一个安全的私有聊天软件         |
+-------------------------------------------+
| Website: https://duckchat.akaxin.com      |
+-------------------------------------------+
| Github : https://github.com/duckchat/gaga |
+-------------------------------------------+
"





originDirName=""
httpPort=80
zalyPort=2021
wsPort=2031
sysOS=`uname -s`
operation=""
duckchatDockerName="ccr.ccs.tencentyun.com/duckchat/gaga"
dockerImageExist=false
dockerContainerExist=false
stopDockerContainerId=""
stopDockerName=""
duckchatName="duckchat"
isExit=false
dockerContainerIdExists=false



# check permission
if [ "$sysOS" = "Linux"  ];then
    uid=`id -u`
	if [ $uid -ne 0 ]; then
    	echo "[DuckChat] 需要 root 权限，请使用: " sudo sh $0
    	exit 1
	fi
fi

checkDockerImageExist () {

	imageCounts=`docker images | grep $duckchatDockerName | wc -l`
	if [ $imageCounts -gt 0 ];then
		dockerImageExist=true
	fi

	if [ "$dockerImageExist" = false ];then
		echo
		echo "[DuckChat] duckchat镜像不存在"
		echo "[DuckChat] 开始拉取duckchat镜像"
		docker pull $duckchatDockerName
		if [ $? != 0 ];then
			echo "[DuckChat] 拉取duckchat镜像失败 "
			exit
		else
			dockerImageExist=true
		fi
	fi
}
getStopDockerContainerId () {
	containers=` docker ps -af "name=$stopDockerName" | awk '{print \$1}'`
	OLD_IFS="$IFS"
	IFS=" "
	IFS="$OLD_IFS"
	for container in $containers
	do
		if [ "$container" != "CONTAINER" ];then
			stopDockerContainerId=$container
		fi
	done
	if [ "$stopDockerContainerId" = "" ];then
		echo "[duckchat] 镜像已是停止状态，不需要再次停止"
		exit
	fi
}

checkDockerContainerIdExist () {
	containers=` docker ps -af "name=$duckchatName" | awk '{print \$1}'`
	OLD_IFS="$IFS"
	IFS=" "
	IFS="$OLD_IFS"
	for container in $containers
	do
		if [ "$container" != "CONTAINER" ];then
			dockerContainerIdExists=true
		fi
	done
}

for arg in "$@"
do
	name=`echo $arg | cut -d \= -f 1`
	value=`echo $arg | cut -d \= -f 2`
	case "$name" in
		-h|--help )
echo "\
-http=[port]
	指定http服务端口号，默认为 80
-zaly=[port]
	指定zaly服务器的监听地址与端口，默认为 “:2021”
-ws=[port]
	指定websocket服务器的监听地址与端口，默认为：”:2031”
start
	启动duckchat镜像
stop [-i stopDockerName]
	终止duckchat镜像
ls
	列出所有的镜像
"
		exit 0;;
		-base)
				originDirName=$value
				continue
			;;
		-http)
				httpPort=$value
				continue
			;;
		-zaly)
				zalyPort=$value
				continue
			;;
		-ws)
				wsPort=$value
				continue
			;;
		start)
			if [ -z "$operation" ];then
				operation="start"
			else
				isExit=true
				echo [DuckChat] $operation , start 命令冲突
				exit
			fi
			;;
		stop)
			if [ -z "$operation" ];then
				operation="stop"
			else
				isExit=true
				echo [DuckChat]  $operation , stop 命令冲突
				exit
			fi
			;;
		ls)
			if [ -z "$operation" ];then
				operation="ls"
			else
				isExit=true
				echo [DuckChat] $operation , ls 命令冲突
				exit
			fi
			;;
		-i)
			duckchatName=$value
	    esac
done

if [ "$isExit" = true ];then
	exit
fi

if [  -z "$operation" ];then
	operation="start"
fi

case $operation in
	start)
		if [ "$sysOS" = "Linux"  ] || [ "$sysOS" = "Darwin" ];then
			echo "[Command] docker -v";
			docker -v

			if [ $? != 0 ];then
				echo "[DuckChat] 请根据文档安装Docker服务器集成环境: https://duckchat.akaxin.com/wiki/server/docker.md"
				echo
				exit
			fi
		fi
		checkDockerImageExist
		if [ "$dockerImageExist" = true  ] ; then
			echo "[DuckChat] 启动duckchat镜像"
			if [ $originDirName = ""];then
				originDirName=$(cd $(dirname ${BASH_SOURCE:-$0});pwd)
			fi
			checkDockerContainerIdExist

			if [ $dockerContainerIdExists = true ]; then
				echo "[DuckChat] duckchat镜像已经启动，请使用stop命令，停止运行镜像"
				exit
			fi

			echo
			echo "#" docker run -itd -p $httpPort:80 -p $wsPort:2031 -p $zalyPort:2021 --name $duckchatName -v $originDirName:/home/gaga $duckchatDockerName

			su -c "setenforce 0" 2>/dev/null
			docker run -itd -p $httpPort:80 -p $wsPort:2031 -p $zalyPort:2021 --name $duckchatName -v $originDirName:/home/gaga $duckchatDockerName
			if [ $? != 0 ];then
				echo "[DuckChat] 启动duckchat镜像失败"
				exit
			fi

			chmod -R 777 $originDirName/src

			if [ -f $originDirName/.git/config ]; then
                if [ "$sysOS" = "Linux"  ]; then
                    sed -i  's/remote\"/remote \"/g'  $originDirName/.git/config
                    sed -i  's/branch\"/branch \"/g'  $originDirName/.git/config
                    sed -i  's/filemode = true/filemode = false/g'  $originDirName/.git/config
                elif [ "$sysOS" = "Darwin" ]; then
                    sed -i 'config'  's/remote\"/remote \"/g'  $originDirName/.git/config
                    sed -i 'config' 's/branch\"/branch \"/g'  $originDirName/.git/config
                    sed -i 'config' 's/filemode = true/filemode = false/g'  $originDirName/.git/config
                fi
			fi

			echo "[DuckChat] 请稍后片刻"
			sleep 9
			echo "[DuckChat] 启动duckchat镜像成功"
		fi
		;;
	stop)
		stopDockerName=$duckchatName
		getStopDockerContainerId

		echo "开始终止$stopDockerName 实例: $stopDockerContainerId"
		docker stop $stopDockerContainerId

		if [ $? != 0 ];then
			echo "[DuckChat] 镜像终止失败"
			exit
		fi

		echo "[DuckChat] 终止 $stopDockerName 镜像成功"
		echo "[DuckChat] 开始删除容器 : $stopDockerName"
		docker rm $stopDockerName
		if [ $? != 0 ];then
			echo "[DuckChat] 删除镜像失败"
			exit
		fi
		echo "[DuckChat] 删除容器 $stopDockerName 成功"
		;;
	ls)
		docker ps -a
	;;
esac


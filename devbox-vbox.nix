{
  devbox = {pkgs, config, ...}:
  {
    deployment.targetEnv = "virtualbox";
    deployment.virtualbox = {
      memorySize = 2048;
      vcpu = 1;
      headless = true; # No graphical display

      sharedFolders = {
        sanbase = {
	  hostPath  =  builtins.toString ./.;
	  readOnly = false;
	};
      };

    };

    fileSystems."/var/www/sanbase" = {
      device = "sanbase";
      fsType = "vboxsf";
      options = [ "uid=1000" "gid=1000" ];
    };

    virtualisation.virtualbox.guest.enable = true;
  };
}

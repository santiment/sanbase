{composerEnv, fetchurl, fetchgit ? null, fetchhg ? null, fetchsvn ? null, noDev ? false}:

let
  packages = {};
  devPackages = {};
in
composerEnv.buildPackage {
  inherit packages devPackages noDev;
  name = "santiment-sanbase";
  src = ./.;
  executable = false;
  symlinkDependencies = false;
  meta = {};
}
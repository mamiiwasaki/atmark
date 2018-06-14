<pre>
package com.example.startingpoint;

import java.io.IOException;
import java.io.InputStream;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.ImageView;

public class Camera extends Activity implements OnClickListener{
	ImageView iv;
	ImageButton ib;
	Button b;
	Intent i;
	final static int cameraData=0;
	Bitmap bmp;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO ��ư�������줿�᥽�åɡ�������
		super.onCreate(savedInstanceState);
		setContentView(R.layout.photo);
		initialize();
		InputStream is = getResources().openRawResource(R.drawable.mickey);
		bmp = BitmapFactory.decodeStream(is);
	}

	private void initialize() {
		// TODO ��ư�������줿�᥽�åɡ�������
		iv = (ImageView)findViewById(R.id.ivReturnedPic);
		ib = (ImageButton)findViewById(R.id.ibTackePic);
		b = (Button)findViewById(R.id.bSetWall);
		ib.setOnClickListener(this);
		b.setOnClickListener(this);
	}

	@Override
	public void onClick(View v) {
		// TODO ��ư�������줿�᥽�åɡ�������
		switch(v.getId()){
		case R.id.bSetWall:
			try{
				getApplicationContext().setWallpaper(bmp);
			} catch(IOException e){
				e.printStackTrace();
			}
			break;

		case R.id.ibTackePic:
			i = new Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
			startActivityForResult(i, cameraData);
			break;
		}
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO ��ư�������줿�᥽�åɡ�������
		super.onActivityResult(requestCode, resultCode, data);
		if(resultCode==RESULT_OK){
			Bundle extras = data.getExtras();
			bmp = (Bitmap) extras.get("data");
			iv.setImageBitmap(bmp);
		}
	}
}


</pre>

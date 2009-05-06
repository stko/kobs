package klobs;


/*  JSCOUR - Java Scour search and download interface
 *  Copyright (C) 2000  jscour@priest.com
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

import java.io.*;
import java.security.*;

public class MD5Sum
	{
	// something around 356000 bytes...
	// < 308,032 bytes
	//public static long SCOUR_MD5_BYTE_LIMIT = 10000;
	public static int SCOUR_MD5_BYTE_LIMIT = (300 * 1024);
	private static MessageDigest md = null;

	/**
	 *	Method:		md5Sum 
	 *	Purpose:	calculate the MD5 in a way compatible with how
	 *				the scour.net protocol encodes its passwords
	 *				(incidentally, it also outputs a string identical
	 *				to the md5sum unix command).
	 *	@param		str	the String from which to calculate the sum
	 *	@return		the MD5 checksum
	 */
	public static String md5Sum (String str)
		{
		try
			{
			return md5Sum (str.getBytes ("UTF-8"));
			}
		catch (UnsupportedEncodingException e)
			{
			throw new IllegalStateException (e.getMessage ());
			}
		}

	public static String md5Sum (byte [] input)
		{
		return md5Sum (input, -1);
		}

	public static String md5Sum (byte [] input, int limit)
		{
		try
			{
			if (md == null)
				md = MessageDigest.getInstance ("MD5");

			md.reset ();
			byte [] digest;

			if (limit == -1)
				{
				digest = md.digest (input);
				}
			else
				{
				md.update (input, 0, 
					limit > input.length ? input.length : limit);
				digest = md.digest ();
				}

			StringBuffer hexString = new StringBuffer (); 

			for (int i = 0; i < digest.length; i++) 
				{
				hexString.append (hexDigit (digest [i])); 
				}

			return hexString.toString ();
			}
		catch (NoSuchAlgorithmException e)
			{
			throw new IllegalStateException (e.getMessage ());
			}
		}

	/**
	 *	Method:		hexDigit 
	 *	Purpose:	convert a hex digit to a String, used
	 *				by md5Sum.
	 *	@param		x	the digit to translate
	 *	@return		the hex code for the digit
	 */
	static private String hexDigit (byte x) 
		{
		StringBuffer sb = new StringBuffer ();
		char c;

		// First nibble
		c = (char) ((x >> 4) & 0xf);
		if (c > 9) 
			{
			c = (char) ((c - 10) + 'a');
			} 
		else 
			{
			c = (char) (c + '0');
			}

		sb.append (c);

		// Second nibble
		c = (char) (x & 0xf);
		if (c > 9) 
			{
			c = (char)((c - 10) + 'a');
			} 
		else 
			{
			c = (char)(c + '0');
			}

		sb.append (c);
		return sb.toString ();
		}

	/**
	 *	Method:		getFileMD5Sum 
	 *	Purpose:	get the MD5 sum of a file. Scour exchange
	 *				only counts the first SCOUR_MD5_BYTE_LIMIT
	 *				bytes of a file for caclulating checksums
	 *				(probably for efficiency or better comaprison
	 *				counts against unfinished downloads).
	 *	@param		f the file to read
	 *	@return		the MD5 sum string
	 *	@throws		IOException on IO error
	 */
	public static String getFileMD5Sum (File f)
		throws IOException
		{
		String sum = null;
		FileInputStream in = new FileInputStream (f.getAbsolutePath ());

		byte [] b = new byte [1024];
		int num = 0;
		ByteArrayOutputStream out = new ByteArrayOutputStream ();

		while ((num = in.read (b)) != -1)
			{
			out.write (b, 0, num);

			if (out.size () > SCOUR_MD5_BYTE_LIMIT)
				{
				sum = md5Sum (out.toByteArray (), SCOUR_MD5_BYTE_LIMIT);
				break;
				}
			}

		if (sum == null)
			sum = md5Sum (out.toByteArray (), SCOUR_MD5_BYTE_LIMIT);

		in.close ();
		out.close ();

		return sum;
		}


	public static void main (String [] args)
		throws Exception
		{
		System.out.println (getFileMD5Sum (new File (args [0]))
			+ "  " + args [0]);
		}

	}